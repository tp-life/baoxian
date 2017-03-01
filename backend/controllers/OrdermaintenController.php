<?php
namespace backend\controllers;
use common\models\BrandOffer;
use common\models\MaintenanceOffer;
use common\models\OrderExtend;
use common\models\OrderMaintenance;
use common\models\OrderMaintenanceService;
use common\models\RoleAccess;
use common\models\RoleModule;
use common\models\Seller;
use Yii;
use common\models\Role;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdermaintenController implements the CRUD actions for OrderMaintenance model.
 */
class OrdermaintenController extends LoginedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
		return [];
    }

    /**
     * Lists all Role models.
     * @return mixed
     */
	public function actionIndex()
	{
		if (Yii::$app->request->isAjax) {
			$respon = array();
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量
			$conditon = array();
			$params = array();
			$query = new Query();
			$query->select('a.*,b.service_status')->from(['a' => OrderMaintenance::tableName()])->leftJoin(['b'=>OrderMaintenanceService::tableName()],'a.id=b.m_order_id')->where($conditon,$params);

			$query->andWhere('b.service_status!=0 or ISNULL(b.service_status)');//重新指派时 要废弃已经指派的商家订单状态
			if ($order_sn = trim(Yii::$app->request->post('order_sn', ''))) {
				$query->andWhere('a.order_sn =:order_sn', [':order_sn' => $order_sn]);
			}
			if ($type=intval(Yii::$app->request->post('type', '0'))) {
				$query->andWhere('a.type=:type', [':type' => $type]);
			}
			if ($contact = trim(Yii::$app->request->post('contact', ''))) {
				$query->andWhere(['like','a.contact',$contact]);
			}
			if ($contact_number = trim(Yii::$app->request->post('contact_number', ''))) {
				if(preg_match('/1[356789][0-9]{9}/',$contact_number)){
					$query->andWhere('a.contact_number =:contact_number', [':contact_number' => $contact_number]);
				}
			}
			$state = trim(Yii::$app->request->post('state', ''));
			if ($state !== '') {
				$query->andWhere('a.state =:state', [':state' => $state]);
			}
			if ($add_time_from = Yii::$app->request->post('add_time_from', '')) {
				$query->andFilterCompare('a.add_time', strtotime($add_time_from), '>=');
			}
			if ($add_time_to = Yii::$app->request->post('add_time_to', '')) {
				$add_time_to = $add_time_to . " 23:59:59";
				$query->andFilterCompare('a.add_time', strtotime($add_time_to), '<=');
			}
			if ($service_status= Yii::$app->request->post('service_status', '')) {
				$query->andWhere('b.service_status =:service_status', [':service_status' => $service_status]);
			}

			$total = $query->count('a.id');
			$data = $query->orderBy('a.id DESC')->limit($pageSize)->offset($start)->all();

			$typeData = OrderMaintenance::typeData();
			$stateData = OrderMaintenance::stateData();
			$statusDate = OrderMaintenanceService::serviceStateData();

			if ($data) {
				foreach ($data as $item) {
					$btn = '<a class="btn red btn-xs  btn-default"  href="' . $this->createUrl(['ordermainten/view', 'id' => $item['id']]) . '"><i class="fa fa-share"></i> 查看</a>';
					$respon[] = [
						Html::a($item['order_sn'],['order/view','id'=>$item['order_id']],['target'=>'_blank','title'=>'订单详情']),
						$typeData[$item['type']],
						$item['contact'],
						$item['contact_number'] ,
						$stateData[$item['state']],
						$item['service_status'] ? $statusDate[$item['service_status']]:'',
						$item['add_time']?date('Y-m-d H:i',$item['add_time']):'',
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index');
	}

    /**
     * Displays a single Role model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);
		$orderExtend = OrderExtend::findOne(['order_id'=>$model['order_id']]);
        return $this->render('view', [
            'model' => $model,
			'orderExtend'=>$orderExtend
        ]);
    }

    /**
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderMaintenance::findOne($id)) !== null) {
            return $model;
        } else {
			if(Yii::$app->request->isAjax){
				return $this->getCheckNo('查无维保记录');
			}
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionChangestate()
	{
		$model = $this->findModel(Yii::$app->request->post('id',0));
		if($model->changeOrderState(Yii::$app->request->post('state'),Yii::$app->request->post('note'))){
			return $this->getCheckYes();
		}
		return $this->getCheckNo('修改状态失败#'.var_export($model->getErrors()));
	}
	public function actionZhipaisearch()
	{
		$key = Yii::$app->request->post('_tp','');
		$value = Yii::$app->request->post('_tp_value','');
        $order_id = Yii::$app->request->post('order_id','');
		if(empty($key) || empty($value) || empty($order_id)){
			return $this->getCheckNo('参数缺失，请重试');
		}
		$order = OrderExtend::findOne(['order_id'=>$order_id]);
        if(!$order){
            return $this->getCheckNo('查无匹配记录');
        }
        $offer=BrandOffer::findOne(['brand_id'=>$order->brand_id,'model_id'=>$order->model_id]);
        if(!$offer){
            return $this->getCheckNo('无当前品牌型号报价信息');
        }

		$data = Seller::find()->where("is_repair=1 AND status=1 AND $key like ('%$value%')")->orderBy('seller_id DESC')->limit(500)->asArray()->all();
        $seller_ids = array_column($data,'seller_id');
        $main_offer = MaintenanceOffer::find()->where(['seller_id'=>$seller_ids,'status'=>1,'offer_id'=>$offer->offer_id])->asArray()->all();
        $main_seller_id = array_column($main_offer,'seller_id');
        foreach ($data as $key=>$val){
            if(!in_array($val['seller_id'],$main_seller_id)){
                unset($data[$key]);
            }
        }
        if(empty($data)){
			return $this->getCheckNo('查无匹配记录');
		}
		return $this->getCheckYes($data);
		//print_r($data);

	}
	/**
	 * 商家指派
	 * [m_id] => 1
	 * [zhipai_note] => 小地方的说法
	 * [id] => 420
	*/
	public function actionZhipaiseller()
	{
		$type = intval(Yii::$app->request->post('type',0));
		if(!$type){
			return $this->getCheckNo('无效理赔类型');
		}
		$model = $this->findModel(Yii::$app->request->post('id',0));

		$seller = Seller::findOne(['is_repair'=>1,'status'=>1,'seller_id'=>Yii::$app->request->post('m_id',0)]);
		if(empty($seller)){
			return $this->getCheckNo('指派商户无效');
		}
		if($model->zhipaiSeller($seller,Yii::$app->request->post('zhipai_note',''))){
			if($model['type'] != $type){
				$model->type = $type;
				$model->update(false,['type']);
			}
			return $this->getCheckYes();
		}
		return $this->getCheckNo('指派失败,请联系管理员');
	}

	/**
	 * 理赔状态更新
	 *  [service_status] => 1
	 * [service_note] => 速度发顺丰
	 * [m_order_id] => 420
	 * [m_order_service_id] => 2
	 * [is_show]=>1
	*/
	public function actionLipei()
	{
		$model = $this->findModel(Yii::$app->request->post('m_order_id',0));
		$serviceModel = OrderMaintenanceService::findOne(['id'=>Yii::$app->request->post('m_order_service_id',0)]);
		if(empty($serviceModel)){
			return $this->getCheckNo('无效指派商家');
		}
		$note = Yii::$app->request->post('service_note','');
		$is_show = Yii::$app->request->post('is_show',0);
		$return  = $model->serviceLipei($serviceModel,Yii::$app->request->post('service_status',1),$note,$is_show);
		return  $return? $this->getCheckYes():$this->getCheckNo('理赔更新失败');
	}


}
