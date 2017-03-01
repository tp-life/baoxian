<?php

namespace common\modules\mweixin\controllers;
use common\library\helper;
use common\models\CardCouponsGrant;
use common\models\CardCouponsLog;
use common\models\CardGrantRelation;
use common\models\InsuranceCoverage;
use common\models\Member;
use common\models\MemberExtend;
use common\models\Order;
use common\models\OrderExtend;
use common\models\OrderLog;
use common\models\Seller;
use common\tool\Sms;
use weixin\components\BaseController;
use Yii;
use yii\base\Exception;

/**
 * api for 购买保险 处理流程
*/

class BuyController extends BaseController
{


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $post = Yii::$app->request->post();
        $post['tel']=trim($post['tel']);
        if(empty($post['coverage_id']) || empty($post['brand'])  || empty($post['model']) || empty($post['code']) || empty($post['tel']) ){
            return $this->getCheckNo('参数错误');
        }
        $coverage_info = InsuranceCoverage::findOne(['id'=>$post['coverage_id']]);
        if(!$coverage_info){
            return $this->getCheckNo('当前保险不存在');
        }
        $trant = Yii::$app->db->beginTransaction();
        try{
            $member=$this->__checkUser($post['tel']);
            if(!$member){
                $insert_member = $this->_createMember($post['tel']);
                if(!$insert_member){
                    throw new Exception('创建用户失败');
                }
                $insert_data=[
                    'member_id'=>$insert_member->member_id,'member_name'=>$insert_member->name,'member_phone'=>$insert_member ->phone
                ];
            }else{
                $insert_data=[
                    'member_id'=>$member->member_id,'member_name'=>$member->name,'member_phone'=>$member ->phone
                ];
            }
            $setData=$this->_createOrder($coverage_info,$insert_data);
            if($setData){
                $seller_id = Seller::$lehuanxin[0];
                $order_extend= [
                    'order_id'=>$setData['order_id'],'seller_id'=>$seller_id,
                    'seller_name'=>Seller::getSellerInfo($seller_id)->seller_name,
                    'buyer'=>'','buyer_phone'=>$insert_data['member_phone'],
                    'brand_id'=>$post['brand'],'model_id'=>$post['model']
                ];
                $model = new OrderExtend();
                $model->setAttributes($order_extend);
                if($model->save()){
                    $trant->commit();
                    return $this->getCheckYes($setData);
                }
            }
            throw new Exception('订单创建失败');
        }catch (Exception $e){
            $trant ->rollBack();
            return $this->getCheckNo($e->getMessage());
        }
    }

    /**
     * 激活选择品牌模型
     * @return array
     */
    public function  actionActivesone(){
        $post = Yii::$app->request->post();
        $post['tel']=trim($post['tel']);
        if(empty($post['brand']) || empty($post['model']) || empty($post['code']) || empty($post['tel'])){
            return $this->getCheckNo('参数错误');
        }
        if(!is_numeric($post['brand']) || !is_numeric($post['model'])){
            return $this->getCheckNo('提交数据错误！');
        }
        //check $post['code']
        $staus=Sms::checkVerifyCode($post['tel'],$post['code']);
        if(!$staus){
            return $this->getCheckNo('验证码错误！');
        }
        $key = $this->member_id.'_active'.$this->token;
        $data=['brand'=>$post['brand'],'model'=>$post['model'],'tel'=>$post['tel'],'time'=>date('H:i:s')];
        $bstop=Yii::$app->cache->set($key,json_encode($data),24*3600);
        return $bstop?$this->getCheckYes([],'成功'):$this->getCheckNo('失败');
    }

    /**
     * 卡券激活第二步
     * @return array
     */
   public function actionActive(){
       $post = Yii::$app->request->post();
       if(empty($post['active_code']) || empty($post['name']) || empty($post['imei']) || empty($post['id_card'])){
           return $this->getCheckNo('参数错误');
       }
       if (!preg_match('/\w{10,20}/', $post['imei'])) {
           return $this->getCheckNo('IMEI 格式错误');
       }
       if (!preg_match('/[\x{4e00}-\x{9fa5}A-Za-z]{2,10}/u', $post['name'])) {
           return $this->getCheckNo('请输入机主姓名');
       }
       if (strlen($post['id_card']) != 15 && strlen($post['id_card']) != 18) {
           return $this->getCheckNo('您输入的机主有效身份证号码');
       }
       $status = $this->_check_code($post['active_code']);
       if(!$status){
          return $this->getCheckNo('该激活码不可用');
       }
       /**
        * 检查imei 是否重复投保
        */
       if(OrderExtend::checkImei($post['imei'])){
           return $this->getCheckNo('当前Imei号已经投保');
       }
       /**
        * 检测秘钥是否处于回款冻结期
        */
       $grant=CardGrantRelation::find()->where(['card_number'=>$status->card_number,'to_seller_id'=>$status->seller_id])->orderBy('id DESC')->one();
       if($grant && $grant->deadline < time() && $grant->deadline > 1000 ){
           return $this->getCheckNo( '该激活码冻结中。。。。');
       }

       $key = $this->member_id.'_active'.$this->token;
       if(!$cache = Yii::$app->cache->get($key)){
            return $this->getCheckNo('操作已过期，请重新操作');
       }
       $coverage_info = InsuranceCoverage::findOne(['id'=>$status->coverage_id]);
       if(!$coverage_info){
           return $this->getCheckNo('当前激活码无对应保险信息');
       }

       $tran = Yii::$app->db->beginTransaction();
       $cache = json_decode($cache,true);
        try{
            $insert_data=['payment_code'=>'kaquan','payment_time'=>time(),'order_state'=>Order::__ORDER_PAY];
            $member=$this->__checkUser($cache['tel']);
            if(!$member){
                $insert_member = $this->_createMember($cache['tel']);
                if(!$insert_member){
                    throw new Exception('创建用户失败');
                }
                $insert_data['member_id']=$insert_member->member_id;
                $insert_data['member_name'] = $insert_member->name;
                $insert_data['member_phone'] = $insert_member->phone;
            }else{
                $insert_data['member_id']=$member->member_id;
                $insert_data['member_name'] = $member->name;
                $insert_data['member_phone'] = $member ->phone;
            }
            $create_data = $this->_createOrder($coverage_info,$insert_data,'卡券激活下单');
            if(!$create_data){
                throw  new Exception('订单生成失败');
            }
            $order_extend= [
                'order_id'=>$create_data['order_id'],'seller_id'=>$status->seller_id,
                'seller_name'=>Seller::getSellerInfo($status->seller_id)->seller_name,
                'buyer'=>$post['name'],'buyer_phone'=>$cache['tel'],
                'imei_code'=>$post['imei'],'idcrad'=>$post['id_card'],
                'brand_id'=>$cache['brand'],'model_id'=>$cache['model']
            ];
            $model = new OrderExtend();
            $model->setAttributes($order_extend);
            $status -> status = CardCouponsGrant::__STATUS_ACTIVE;
            $status -> order_id = $create_data['order_id'];
            $status -> active_time =time();
            $log=['hand_type'=>CardCouponsLog::__TYPE_ACTIVE,'from_seller_id'=>$status->seller_id,
                'message'=>$post['name'].'['.$this->member_id.']激活卡券('.$status->coverage_code.')','to_seller_id'=>$status->seller_id];
            CardCouponsLog::addLog($log);
            $ret = $status ->save();
            if($model->save() && $ret){
                $tran->commit();
                return $this->getCheckYes(['order_id'=>$create_data['order_id']]);
            }
            throw  new Exception('订单激活失败');
        }catch (Exception $e){
            $tran->rollBack();
            return $this->getCheckNo($e->getMessage());
        }

   }

	/**
	 * 激活资料最后一步，提交照片
	 * @return array
	 *
	 * eg:
	 * Array
	 * (
	 * [face_image] => Array
	 * (
	 * [name] => 09193US0-8.jpg
	 * [type] => image/jpeg
	 * [tmp_name] => D:\xampp\tmp\phpEA27.tmp
	 * [error] => 0
	 * [size] => 32803
	 * )
	 *
	 * [back_image] => Array
	 * (
	 * [name] => 09193US0-8.jpg
	 * [type] => image/jpeg
	 * [tmp_name] => D:\xampp\tmp\phpEA38.tmp
	 * [error] => 0
	 * [size] => 32803
	 * )
	 *
	 * )
	 * Array
	 * (
	 * [token] => A1E0D199BE6A77E6E7E98EF7E55828A1
	 * [order_id] => 28
	 * )
	 */
	public function actionPrefect()
	{
		$post = Yii::$app->request->post();
//		$upload_face_image = $this->updateFile('face_image');
//		if ($upload_face_image['code'] == 400) {
//			return $this->returnBack($upload_face_image);
//		}
//		$post['face_image'] = $upload_face_image['path'];
//
//		$upload_back_image = $this->updateFile('back_image');
//		if ($upload_back_image['code'] == 400) {
//			return $this->returnBack($upload_back_image);
//		}
//		$post['back_image'] = $upload_back_image['path'];
        $post['back_image']=trim($post['back_image']);
        $post['face_image'] = trim($post['face_image']);
		if (empty($post['order_id']) ) {
			return $this->getCheckNo('参数错误');
		}
		if(!is_file(ltrim($post['face_image'],'/'))){
			return $this->getCheckNo('手机正面图片处理失败，请重新上传');
		}
		if(!is_file(ltrim($post['back_image'],'/'))){
            return $this->getCheckNo('手机背面图片处理失败，请重新上传');
        }

		$order_status = [Order::__ORDER_PAY, Order::__ORDER_APPLF_ERR];
		$condition = ['order_state' => $order_status, 'order_id' => $post['order_id']];
		$result = Order::findOne($condition);
		if (!$result) {
			return $this->getCheckNo('当前订单不存在，或该订单已资料已经完善');
		}
		$extends = OrderExtend::findOne(['order_id' => $post['order_id']]);
		if (!$extends) {
			return $this->getCheckNo('请先完善该订单的基础信息！');
		}
		$tran = Yii::$app->db->beginTransaction();
		try {
			$extends->imei_face_image = $post['face_image'];
			$extends->imei_back_image = $post['back_image'];
			$extends->is_data = 1;
			if ($extends->save()) {
				$result->order_state = Order::__ORDER_APPLF;
				if ($result->save()) {
                    helper::sendSms('order',['tel'=>$extends->buyer_phone],Sms::TYPE_ORDER);
					$tran->commit();
					return $this->getCheckYes();
				}
			}
			throw  new Exception('资料信息完善失败');
		} catch (Exception $e) {
			$tran->rollBack();
			return $this->getCheckNo($e->getMessage());
		}
	}


    /**
     * 检查秘钥是否可用
     * @return array
     */
   public function actionCheckactice(){
        $code = Yii::$app->request->post('active_code','');
       if(!$code){
           return $this->getCheckNo('参数错误');
       }
       $status=$this->_check_code($code);
       return $status? $this->getCheckYes([]):$this->getCheckNo('该秘钥不可用');
   }

    /**
     * 检测该激活秘钥是否有用
     * @param string $active_code
     * @return bool
     */
   private function _check_code($active_code=''){
       if(!$active_code) return false;
       $condition = [
           'card_secret'=>$active_code,
           'status'=>CardCouponsGrant::__STATUS_DEFAULT
       ];
       $model = CardCouponsGrant::findOne($condition);
       if(in_array($model->seller_id,Seller::$lehuanxin)){
           $model =false;
       }
       return $model;
   }

    /**
     * 创建订单
     * @param $coverage_info
     * @param array $inser_data
     * @param string $msg
     * @return array|bool
     */
   private function _createOrder($coverage_info,$inser_data=[],$msg='购买下单'){
       $model= new Order();
       $member_info = [
           'member_id' => $this->member_id,
           'phone'=>$this->member_phone,
           'name'=>$this->member_name
       ];
       $order_sn=helper::_makeOrderSn();
       $insert = [
           'order_sn'=>$order_sn,
           'member_id' =>$member_info['member_id'],
           'coverage_id'=>$coverage_info->id,
           'coverage_name'=>$coverage_info->coverage_name,
           'coverage_code' =>$coverage_info->coverage_code,
           'coverage_type' =>$coverage_info ->type_id,
           'order_state' => 10,
           'order_amount'=>$coverage_info->official_price,
           'coverage_price'=>$coverage_info->official_price,
           'number'=>1,
           'add_time'=>time(),
           'member_name'=>$member_info['name'],
           'member_phone'=>$member_info['phone']
       ];
       $insert =array_merge($insert,$inser_data);
       $model->setAttributes($insert);

       if($model->save()){
           $log_data=[
               'order_id'=>$model->order_id,
               'before_order_state'=>10,
               'order_state'=>10,
               'log_msg' => $msg,
               'log_user'=>$member_info['name'],
               'log_time'=>date('Y-m-d H:i:s'),
               'status'=>1
           ];
           $log_model = new OrderLog();
           $log_model->insertLog($log_data);
           $returnData=[
               'order_sn'=>$order_sn,
               'price' =>$model->order_amount,
               'order_id'=>$model->order_id
           ];
           return $returnData;
       }
       return false;
   }


   private function __checkUser($phone=''){
        $model=Member::findOne(['phone'=>$phone]);
        return $model;
   }

   private  function _createMember($phone=''){
       $phone =trim($phone);
       if(!$phone){
           return false;
       }
       $model =new Member();
       $model->setPassword($phone);
       $model->name = $phone;
       $model->phone = $phone;
       if($model->save()){
           $member_extend = new MemberExtend();
           $member_extend->member_id = $model->member_id;
           $member_extend->register_time = time();
           if($member_extend->save()){
               return $model;
           }
       }
       return false;
   }
    
}
