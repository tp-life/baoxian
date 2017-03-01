<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\library\helper;
use common\models\Area;
use common\models\Bank;
use common\models\Member;
use Yii;
use common\models\Seller;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SellerController implements the CRUD actions for Seller model.
 */
class MainterController extends LoginedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Seller models.l
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionGetdata()
    {


        $filed=Yii::$app->request->post('filter','');
        $val=Yii::$app->request->post('status','');
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $s=Seller::tableName();
        $b=Bank::tableName();
        $user=Seller::find()->leftJoin($b.' as bank','bank.member_id = '.$s.'.member_id')->select($s.'.*,bank.brank_name,bank.account_holder,bank.brank_account')->where([$s.'.is_repair'=>1]);

        if($filed){

            $user->andWhere(['or',$s.'.seller_name like \'%'.$filed.'%\'',$s.'.seller_id = '. (int)$filed]);
        }
        if($val){
            $user->andWhere([$s.'.status'=>$val - 1]);
        }
        $count=$user->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' =>$user->orderBy($s.'.seller_id desc')->limit($pageSize)->offset($start)->asArray(),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);

        $member = $dataProvider->getModels();

        $data=[
            'draw'=>intval($_REQUEST['draw']),
            'recordsTotal'=>$count,
            'recordsFiltered'=>$count,
            'data'=>[]
        ];
        foreach($member as $key=>$val){
            $btn = '<a class="btn green btn-xs  btn-default" href="' . $this->createUrl(['seller/view', 'id' => $val['seller_id']]) . '"><i class="fa fa-share"></i> 查看</a>';
            $btn .= '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['seller/perfect', 'member_id' => $val['member_id']]) . '" ><i class="fa fa-pencil">修改</i></a>';
            $btn .= $val['status']==1?
                '<a class="btn red btn-xs btn-default " onClick="handleStatus('.$val['seller_id'].','. 0 .')"  href="javascript:;"><i class="fa fa-trash-o"></i> 终止合作 </a>'
            :'<a class="btn blue btn-xs btn-default" onClick="handleStatus('.$val['seller_id'].','. 1 .')" href="javascript:;"><i class="fa fa-check"></i> 重启合作 </a>';

            $data['data'][]=array(
//                '<input type="checkbox" name="id[]" value="'.$val['seller_id'].'">',
                $val['seller_id'],
                $val['seller_name'],
                Member::findOne(['member_id'=>$val['member_id']])->name,
                $val['area_info'].$val['detail_address'],
                $val['concat'],
                $val['concat_tel'],
                $val['brank_name'].' , '.$val['account_holder'].' , '.$val['brank_account'],
                $val['status']==1?'<span class="label label-sm label-success">合作中</span>':'<span class="label label-sm label-danger">已终止</span>',
                $btn
            );
        }

        return json_encode($data);
    }


    public function actionChange(){
        if(!Yii::$app->request->isAjax || !Yii::$app->request->isPost){
            return $this->getCheckNo('非法访问!');
        }
        $seller_id =Yii::$app->request->post('seller_id');
        $status=Yii::$app->request->post('status',null);
        if(!$seller_id || is_null($status)){
            return $this->getCheckNo('参数错误!');
        }
        $seller = Seller::findOne(['seller_id'=>$seller_id]);
        if($seller){
            $seller->status = (int) $status;
            if($seller->save()){
                return $this->getCheckYes([],'操作成功!');
            }
        }
        return $this->getCheckNo('操作失败!');
    }

    /**
     * 完善商户信息
     * @return string
     */
    public function actionPerfect(){

        $member_id=Yii::$app->request->get('member_id','');
        $phone=Yii::$app->request->get('phone','');
        if(!$member_id){
            if(!$phone){
                return $this->render('/site/error',['name'=>'访问错误','message'=>'非法访问控制器']);
            }
            $user=Member::findOne(['phone'=>$phone]);
            $member_id=$user->member_id;
        }
        $area=Area::findAll(['area_parent_id'=>0]);
        $seller=Seller::find()->leftJoin('fj_bank as bank','bank.member_id = fj_seller.member_id')->where(['fj_seller.member_id'=>$member_id])->select('*')->asArray()->one();
        $city_html='';
        $area_html='';
        if($seller){
            $city_html=helper::getAreaSelect($seller['province_id'],$seller['city_id']);
            $area_html=helper::getAreaSelect($seller['city_id'],$seller['area_id']);
        }
        return $this->render('perfect',['member_id'=>$member_id,'province'=>$area,'seller'=>$this->viewData($seller),'city_html'=>$city_html,'area_html'=>$area_html]);
    }

    /**
     * 返回默认商户信息资料
     * @param $seller
     * @return array
     */
    private function viewData($seller){
        $seller=is_array($seller)?$seller: [];
        $temp=['brank_name'=>'','brank_account'=>'','account_holder'=>'','seller_name'=>'','is_insurance'=>'','is_repair'=>'','province_id'=>'',
        'city_id'=>'','area_id'=>'','detail_address'=>'','concat'=>'','concat_tel'=>''];
        return array_merge($temp,$seller);
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

    /**
     * Displays a single Seller model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $seller=$this->findModel($id);
        return $this->render('view', [
            'model' =>$seller ,
            'bank' =>Bank::findOne(['member_id'=>$seller->member_id])
        ]);
    }

    /**
     * Creates a new Seller model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(!Yii::$app->request->isPost || !Yii::$app->request->isAjax){
            return $this->render('/site/error',['name'=>'访问错误','message'=>'非法访问']);
        }
        $post=Yii::$app->request->post();
        if(!$post['member_id'] || !$post['seller_name'] || !$post['concat'] || !$post['concat_tel'] || !$post['province_id'] || !$post['city_id'] || !$post['area_id'] || !$post['detail_address']){
            $this->retrunAjax(['status'=>false,'msg'=>'却少必要的参数']);
            return $this->getCheckNo('却少必要的参数');
        }
        $model=Seller::findOne(['member_id'=>$post['member_id']]) ;
        $model=$model?$model:new Seller();
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
        if(isset($post['is_type'])){
//            in_array('1',$post['is_type']) && $model->is_insurance = 1;
//            in_array('2',$post['is_type']) && $model->is_repair=1;

            $model->is_insurance=in_array('1',$post['is_type'])?1:0;
            $model->is_repair=in_array('2',$post['is_type'])?1:0;
        }
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

            return $this->getCheckYes([],'商户信息完善成功');
        }
        $transaction->rollBack();
        return $this->getCheckNo('商户信息完善失败,请稍后再试.');

    }



    /**
     * Finds the Seller model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Seller the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Seller::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
