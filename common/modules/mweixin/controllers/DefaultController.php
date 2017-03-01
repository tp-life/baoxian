<?php

namespace common\modules\mweixin\controllers;

use common\models\Member;
use common\models\MemberExtend;
use common\models\SmsLog;
use common\models\WxMember;
use common\tool\Sms;
use weixin\components\BaseController;
use Yii;
use yii\web\Cookie;

/**
 * Default controller for the `mweixin` module
 * 保险 主题 展示性信息 banner 等
 */
class DefaultController extends BaseController
{
	/**
	 * Renders the index view for the module
	 * @return string
	 */
	public function actionIndex()
	{

	}

	public function actionVerifycode()
	{
		$phone = Yii::$app->request->post('phone', '');
		//$phone = '18612178240';
		$code_type = Yii::$app->request->post('code_type', SMS::TYPE_USER_VERFIY_CODE);
		if (!$code_type) {
			$code_type = SMS::TYPE_USER_VERFIY_CODE;
		}
		if (!preg_match('/^1[34578]{1}\d{9}$/', $phone)) {
			return $this->getCheckNo('手机号码格式错误');
		}
		if (strtoupper($code_type) == SMS::TYPE_USER_VERFIY_CODE) {
			$code = Sms::gainCode($phone);
			$code_message = Sms::getVerifyMessage($code);
			$flag = Sms::sendSMS($phone, $code_message, SMS::TYPE_USER_VERFIY_CODE, ['captcha' => $code]);
			if ($flag) {
				return $this->getCheckYes(['captcha' => $code], '验证码已发送');
			}
			return $this->getCheckNo('验证码发送失败');
		}
		return $this->getCheckNo('无效类型');
	}


	public function actionCheckverifycode()
	{
		$phone = Yii::$app->request->post('phone', '');
		$code_type = Yii::$app->request->post('code_type', Sms::TYPE_USER_VERFIY_CODE);
		$captcha = Yii::$app->request->post('captcha', '');
		$staus = Sms::checkVerifyCode($phone, $captcha, $code_type);
		if (!$staus) {
			return $this->getCheckNo('验证码错误');
		}
		return $this->getCheckYes([], '验证码有效');
	}

	/**
	 * 执行微信绑定登录
	 */
	public function actionLogin()
	{
		if(!Yii::$app->request->isAjax){
			if($this->member_id){
				//查看保单列表
				Yii::$app->response->cookies->add(new Cookie(['name'=>'isLogin','value'=>1,'httpOnly'=>false]));
				return $this->redirect('/weixin/ordersList.html');
			}
			//否则登录
			return $this->redirect('/weixin/login.html');
		}


		$phone = Yii::$app->request->post('phone', '');
		$captcha = Yii::$app->request->post('captcha', '');

		$staus = Sms::checkVerifyCode($phone, $captcha, Sms::TYPE_USER_VERFIY_CODE);
		if (!$staus) {
			return $this->getCheckNo('验证码错误');
		}
		if($this->member_id){
			Yii::$app->response->cookies->add(new Cookie(['name'=>'isLogin','value'=>1,'httpOnly'=>false]));
			return $this->getCheckYes(['token'=>$this->token],'已经绑定手机号码');
		}
		//app 端介入
		if (Yii::$app->request->cookies->getValue('retailClient', '') === 'retail') {

			$hasMember = Member::findOne(['phone' => $phone]);
			if ($hasMember) {


				Yii::$app->response->cookies->add(new Cookie(['name' => 'wxToken', 'value' => $hasMember['member_id'], 'httpOnly' => false]));
				Yii::$app->response->cookies->add(new Cookie(['name' => 'isLogin', 'value' => 1, 'httpOnly' => false]));
				Yii::$app->response->cookies->add(new Cookie(['name' => 'member_token', 'value' => $hasMember['member_id'], 'httpOnly' => false]));
				return $this->getCheckYes(['member_id' => $hasMember['member_id'],
					'openid' => '',
					'token' => $hasMember['member_id'],
					'nickname' => $hasMember['phone'],
					'datetime' => '']);
			}
			$member = new Member();
			$member->setPassword($captcha);
			$member->name = $phone;
			$member->phone = $phone;
			if ($member->save(false)) {
				$member_extend = new MemberExtend();
				$member_extend->member_id = $member->member_id;
				$member_extend->register_time = time();
				$member_extend->save(false);
				return $this->getCheckYes(['member_id' => $hasMember['member_id'],
					'openid' => '',
					'token' => $hasMember['member_id'],
					'nickname' => $hasMember['phone'],
					'datetime' => '']);
			}
			return $this->getCheckNo('绑定登录异常');
		}


		$hasMember = Member::findOne(['phone' => $phone]);
		if ($hasMember) {
			//return $hasMember;
			//绑定微信用户
			if ($bdm = $this->bandingWx($hasMember)) {
				return $this->getCheckYes($bdm);
			} else {
				return $this->getCheckNo('微信绑定登录失败');
			}
		}
		$member = new Member();
		$member->setPassword($captcha);
		$member->name = $phone;
		$member->phone = $phone;
		if ($member->save(false)) {
			$member_extend = new MemberExtend();
			$member_extend->member_id = $member->member_id;
			$member_extend->register_time = time();
			$member_extend->save(false);
			//绑定微信用户
			if ($bdm = $this->bandingWx($member)) {
				return $this->getCheckYes($bdm);
			} else {
				return $this->getCheckNo('微信绑定登录失败');
			}
		} else {
			return $this->getCheckNo('微信用户绑定登录异常');
		}
	}

	protected function bandingWx($member)
	{
		$token = $_REQUEST['token'];
		if (!$token) {
			return false;
		}
		$wxModel = WxMember::findOne(['token' => $token]);
		if ($wxModel) {
			if (!$wxModel->member_id) {
				$wxModel->member_id = $member->member_id;
				$wxModel->datetime = date('Y-m-d H:i:s');
				$wxModel->token = md5($member->member_id . $wxModel->openid);
				if ($wxModel->update(false, ['member_id', 'datetime', 'token'])) {
					Yii::$app->session->set('authInfo', $wxModel->getAttributes());
					//return $this->getCheckYes($wxModel->getAttributes());
				}
				$member->name = $wxModel['nickname'];
				$member->update(false,['name']);
			}
			Yii::$app->response->cookies->add(new Cookie(['name'=>'wxToken','value'=>$wxModel['token'],'httpOnly'=>false]));
			Yii::$app->response->cookies->add(new Cookie(['name'=>'isLogin','value'=>1,'httpOnly'=>false]));
			Yii::$app->response->cookies->add(new Cookie(['name'=>'member_token','value'=>$wxModel['token'],'httpOnly'=>false]));
			return [
				'member_id' => $wxModel['member_id'],
				'openid' => $wxModel['openid'],
				'token' => $wxModel['token'],
				'nickname' => $wxModel['nickname'],
				'datetime' => $wxModel['datetime']
			];
		}
		return false;
	}

	public function actionLoginout(){
        if (Yii::$app->request->cookies->getValue('retailClient', '') === 'retail'){
            Yii::$app->response->cookies->remove(new Cookie(['name'=>'isLogin']));
            return $this->getCheckYes('已注销手机绑定');
        }
        $token = $_REQUEST['token'];

        $wxModel = WxMember::findOne(['token' => $token]);
        if ($wxModel) {
            $wxModel->member_id = 0;
            $wxModel->datetime = date('Y-m-d H:i:s',time());
            $wxModel->update(false,['member_id','datetime']);
            Yii::$app->response->cookies->remove(new Cookie(['name'=>'isLogin']));
            return $this->getCheckYes('已注销手机绑定');
        }
        return $this->getCheckNo('注销手机绑定异常，稍后再试');
    }


	/**
	 * 统一二进制流上传处理
	 */
	public function actionBinaryfile()
	{
		if (!Yii::$app->request->isPost && !Yii::$app->request->isAjax) {
			return $this->getCheckNo('非法请求');
		}
		return $this->returnBack($this->uploadBinaryFile());
	}

}
