<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/9/22
 * Time: 下午2:32
 */

namespace maintainer\controllers;

use common\library\helper;
use common\models\Area;
use common\models\Bank;
use common\models\Member;
use common\models\Seller;
use maintainer\components\BaseController;
use maintainer\components\LoginedController;
use Yii;

class AccountController extends LoginedController
{

    public function actionIndex(){
        $member_id=Yii::$app->user->identity->id;
        if(!$member_id){
            return $this->showMessage('非法访问控制器');
        }
        $user=Member::findOne(['member_id'=>$member_id]);
        $area=Area::findAll(['area_parent_id'=>0]);
        $seller=Seller::find()->leftJoin('fj_bank as bank','bank.member_id = fj_seller.member_id')->where(['fj_seller.member_id'=>$member_id])->select('*')->asArray()->one();
        if($seller){
            $seller['parent_name']=$seller['pid']?Seller::findOne(['seller_id'=>$seller['pid']])->seller_name:'';
        }
        return $this->render('index',['member_id'=>$member_id,'province'=>$area,'seller'=>$this->viewData($seller),'user'=>$user]);
    }



    public function actionCreate(){
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
            $post=Yii::$app->request->post();
            if($post['account_holder']){
				if (!preg_match('/^[\x4e00-\x9fa5]{2,5}/', $post['account_holder'])) {
					return $this->getCheckNo('开户人必须是中文汉字');
				}
			}
			if($post['brank_account']){
            	if(strpos($post['brank_name'],'支付宝')!==false){

				}else{
					if (!preg_match('/\d{15,20}/', $post['brank_account'])) {
						return $this->getCheckNo('银行卡号格式错误');
					}
				}

			}

            if(!$post['member_id'] || !$post['seller_name'] || !$post['concat'] || !$post['concat_tel'] || !$post['province_id'] || !$post['city_id'] || !$post['area_id'] || !$post['detail_address']){
                return $this->getCheckNo('参数缺失，请按要求填写');
            }
            $model=Seller::findOne(['member_id'=>$post['member_id']]) ;
            $transaction=$model->getDb()->beginTransaction();
            $model->seller_name=$post['seller_name'];
            $model->member_id=intval($post['member_id']);
            $model->concat=$post['concat'];
            $model->concat_tel=$post['concat_tel'];
            list($province_id,$procince)=explode(',',$post['province_id']);
            list($city_id,$city)=explode(',',$post['city_id']);
            list($area_id,$area)=explode(',',$post['area_id']);

            $model->province_id=$province_id;
            $model->city_id=$city_id;
            $model->area_id=$area_id;
            $model->area_info=$procince.' '.$city.' '.$area;
            $model->detail_address=$post['detail_address'];
            $model->add_time=time();
            $t=$model->save();
            $t1=true;
            if($post['brank_name'] && $post['brank_account']){
                $model_bank=Bank::findOne(['member_id'=>$post['member_id']]);
                $model_bank=$model_bank?$model_bank:new Bank();
                $model_bank->member_id=intval($post['member_id']);
                $model_bank->account_holder=$post['account_holder'];
                $model_bank->brank_account=$post['brank_account'];
                $model_bank->brank_name=$post['brank_name'];
                $model_bank->add_time=time();
                if(!$model_bank->save()){
                    $t1=false;
                }
            }
            if($t && $t1){
                $transaction->commit();

                return $this->getCheckYes([],'商户信息修改成功');
            }
            $transaction->rollBack();
            return $this->getCheckNo('商户信息修改失败,请稍后再试.');
        }

        $member_id=Yii::$app->user->identity->id;
        if(!$member_id){
            return $this->showMessage('非法访问控制器');
        }
        $user=Member::findOne(['member_id'=>$member_id]);
        $area=Area::findAll(['area_parent_id'=>0]);
        $seller=Seller::find()->leftJoin('fj_bank as bank','bank.member_id = fj_seller.member_id')->where(['fj_seller.member_id'=>$member_id])->select('*')->asArray()->one();

        $city_html='';
        $area_html='';
        if($seller){
            $seller['parent_name']=$seller['pid']?Seller::findOne(['seller_id'=>$seller['pid']])->seller_name:'';
            $city_html=helper::getAreaSelect($seller['province_id'],$seller['city_id']);
            $area_html=helper::getAreaSelect($seller['city_id'],$seller['area_id']);
        }
        return $this->render('create',['member_id'=>$member_id,'province'=>$area,'seller'=>$this->viewData($seller),'city_html'=>$city_html,'area_html'=>$area_html,'user'=>$user]);

    }

    /**
     * 获取地区
     */
    public function actionGetarea(){
        $pid=Yii::$app->request->post('id');
        if(!$pid){

            return $this->getCheckNo('却少必要的参数');
        }
        $area=Area::find()->where(['area_parent_id'=>$pid])->asArray()->all();
        return $this->getCheckYes($area,'');
    }

    public  function actionLevel(){
        $search=Yii::$app->request->get('q','');
        if(!$search){
            return json_encode([]);
        }
        $seller=Seller::find()->where(['like','seller_name',$search])->andWhere(['pid'=>0])->all();
        $data=[];
        foreach($seller as $val){
            $data[]=[$val->seller_id,$val->seller_name];
        }
        return json_encode($data);
    }

    public function actionPwd(){
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
            $pwd = Yii::$app->request->post('password','');
            $new_pwd = Yii::$app->request->post('new_pwd','');
            $sure_pwd =Yii::$app->request->post('sure_pwd','');
            if(!$pwd || !$new_pwd || !$sure_pwd){
                return $this->getCheckNo('参数错误');
            }
            if($new_pwd !== $sure_pwd){
                return $this->getCheckNo('两次密码不一致');
            }
            $model = Member::findOne(['name'=>Yii::$app->user->identity->name]);
            if(!$model->validatePassword($pwd)){
                return $this->getCheckNo('原始密码不正确');
            }
            $model->setPassword($new_pwd);
            if($model->save()){
                return $this->getCheckYes('密码修改成功');
            }
            return $this->getCheckNo('密码修改失败');
        }
        $this->render('pwd');
    }

    /**
     * 返回默认商户信息资料
     * @param $seller
     * @return array
     */
    private function viewData($seller){
        $seller=is_array($seller)?$seller: [];
        $temp=['brank_name'=>'','brank_account'=>'','account_holder'=>'','seller_name'=>'','is_insurance'=>'','is_repair'=>'','province_id'=>'',
            'city_id'=>'','area_id'=>'','detail_address'=>'','concat'=>'','concat_tel'=>'','pid'=>'','parent_name'=>''];
        return array_merge($temp,$seller);
    }

}