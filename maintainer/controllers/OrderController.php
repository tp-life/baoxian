<?php

namespace maintainer\controllers;

use common\library\helper;
use common\models\Area;
use common\models\Express;
use common\models\MaintenanceOffer;
use common\models\OrderExtend;
use common\models\OrderMaintenance;
use common\models\OrderMaintenanceLog;
use common\models\OrderMaintenanceService;
use common\models\Seller;
use maintainer\models\UploadForm;
use Yii;
use common\models\Role;
use maintainer\components\LoginedController;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * OrderController implements the CRUD actions for OrderMaintenance model.
 */
class OrderController extends LoginedController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [];
	}

	/**
	 * Lists all Role models.
	 *
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
			$query->select('a.id,a.order_sn,a.service_status,a.add_time,b.type,b.contact,b.contact_number')->from(['a' => OrderMaintenanceService::tableName()])->leftJoin(['b' => OrderMaintenance::tableName()], 'a.m_order_id=b.id')->where($conditon, $params);

			$query->andWhere('a.m_id =:m_id', [':m_id' => $this->seller->seller_id]);
			$query->andWhere('a.service_status!=0');//废弃订单除外

			if ($id = intval(Yii::$app->request->post('id', 0))) {
				$query->andWhere('a.id =:id', [':id' => $id]);
			}
			if ($order_sn = trim(Yii::$app->request->post('order_sn', ''))) {
				$query->andWhere('a.order_sn =:order_sn', [':order_sn' => $order_sn]);
			}
			if ($type = intval(Yii::$app->request->post('type', '0'))) {
				$query->andWhere('b.type=:type', [':type' => $type]);
			}
			if ($contact = trim(Yii::$app->request->post('contact', ''))) {
				$query->andWhere(['like', 'b.contact', $contact]);
			}
			if ($contact_number = trim(Yii::$app->request->post('contact_number', ''))) {
				if (preg_match('/1[356789][0-9]{9}/', $contact_number)) {
					$query->andWhere('b.contact_number =:contact_number', [':contact_number' => $contact_number]);
				}
			}

			if ($add_time_from = Yii::$app->request->post('add_time_from', '')) {
				$query->andFilterCompare('a.add_time', strtotime($add_time_from), '>=');
			}
			if ($add_time_to = Yii::$app->request->post('add_time_to', '')) {
				$add_time_to = $add_time_to . ' 23:59:59';
				$query->andFilterCompare('a.add_time', strtotime($add_time_to), '<=');
			}
			if ($service_status = Yii::$app->request->post('service_status', '')) {
				$query->andWhere('a.service_status =:service_status', [':service_status' => $service_status]);
			}

			$total = $query->count('a.id');
			$data = $query->orderBy('a.id DESC')->limit($pageSize)->offset($start)->all();

			$typeData = OrderMaintenance::typeData();
			$statusDate = OrderMaintenanceService::serviceStateData();

			if ($data) {
				foreach ($data as $item) {
					$btn = '<a class="btn green btn-xs  btn-default" title="点击查看详细" href="' . $this->createUrl(['order/view', 'id' => $item['id']]) . '"><i class="fa fa-share"></i> 查看详细</a>';
					$respon[] = [
						$item['id'],
						$item['order_sn'],
						$typeData[$item['type']],
						$item['contact'],
						$item['contact_number'],
						$item['service_status'] ? $statusDate[$item['service_status']] : '',
						$item['add_time'] ? date('Y-m-d H:i', $item['add_time']) : '',
						$btn,
					];
				}
			}

			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index');
	}

	public function actionView()
	{
        $id=Yii::$app->request->get('id',0);
        $order_id = Yii::$app->request->get('order_id',0);
        if(!$id && !$order_id){
            $this->showMessage('非法访问');
        }
        if($id){
            $model = $this->findModel($id);
        }else{
            $model = OrderMaintenanceService::find()->where(['m_order_id'=>$order_id])->orderBy('id desc')->one();
        }
		$seller = $model->getSellerInfo();
		$order = $model->getOrderInfo();
		$member = $order->getMemberInfo();
		$orderExtend = OrderExtend::findOne(['order_id' => $model['order_id']]);

		return $this->render('view', [
			'model' => $model,
			'seller' => $seller,
			'order' => $order,
			'member' => $member,
			'orderExtend' => $orderExtend,

		]);
	}

	/**
	 * Finds the Role model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id
	 *
	 * @return Role the loaded model
	 *
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = OrderMaintenanceService::findOne($id)) !== null) {
			return $model;
		} else {
			if (Yii::$app->request->isAjax) {
				return $this->getCheckNo('查无维保记录');
			}
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * 商家理赔处理  理赔展示.
	 **/
	public function actionShowlipei()
	{
		$model = $this->findModel(Yii::$app->request->get('order_service_id', 0));
		$seller = $this->seller;
		$order = OrderMaintenance::findOne(['id' => Yii::$app->request->get('order_id', 0)]);
		$orderExtend = OrderExtend::findOne(['order_id' => $model['order_id']]);
		$data = MaintenanceOffer::getSellerOffer($this->seller->seller_id, $orderExtend['brand_id'], $orderExtend['model_id']);
		$city_html = helper::getAreaSelect($seller['province_id'], $seller['city_id']);
		$area_html = helper::getAreaSelect($seller['city_id'], $seller['area_id']);
		$area = Area::findAll(['area_parent_id' => 0]);
		$express = Express::getAllExpress();
		$express_list = ArrayHelper::map($express, 'id', 'e_name');
		return $this->renderPartial('_change_state', [
			'model' => $model,
			'order' => $order,
			'orderExtend' => $orderExtend,
			'data' => $data,
			'seller' => $seller,
			'city_html' => $city_html,
			'area_html' => $area_html,
			'province' => $area,
			'express_list' => $express_list
		]);
	}

	/**
	 * 图片上传.
	 *
	 * @return string
	 */
	public function actionUpload()
	{
		$id = Yii::$app->request->post('order_id', '');
		$key = Yii::$app->request->post('key', '');
		if (empty($id) || empty($key)) {
			$this->responData['message'] = '参数缺失';
			exit(json_encode($this->responData));
		}
		$root = Yii::getAlias('@webroot');
		//$path = $root.'/uploads';
		$path = '/uploads/business/' . date('Ymd');
		if (!FileHelper::createDirectory($root . $path)) {
			$this->responData['message'] = '文件上传权限不足,请联系管理员';
			exit(json_encode($this->responData));
		}
		$model = new UploadForm();
		$model->file = UploadedFile::getInstance($model, 'file');
		if ($model->file && $model->validate()) {
			$baseName = $model->file->baseName;
			$extension = $model->file->extension;
			$file = strtolower($path . '/' . $key . '_' . sha1($baseName . time()) . '.' . $extension);
			if ($model->file->saveAs($root . $file)) {
				//$file = '/'.ltrim($file, strtolower($root));
				$this->responData['code'] = 'yes';
				$this->responData['message'] = 'Success';
				$this->responData['data'] = ['url' => $file, 'key' => $key];
				exit(json_encode($this->responData));
			} else {
				$model->addError('file', '文件保存出错');
			}
		}
		//$this->responData['message'] = var_export($model->getErrors(), true);
		$this->responData['message'] = $model->getFirstError('file');
		exit(json_encode($this->responData));
	}

	/**
	 * [type] => 1
	 * [province_id] => 23,四川
	 * [city_id] => 385,成都市
	 * [area_id] => 4225,青白江区
	 * [detail_address] => 哈哈哈哈
	 * [service_status] => 4
	 * [express_id] => 29
	 * [express_number] => 32665445454346
	 * [offer_info] => 868 //报价编号
	 * [damage_type] => 1 //报价类型
	 * [before_phone_image] => /uploads/business/20161124/before_phone_image_fcb8077a83cf2290fa9
	 * [after_phone_image] => /uploads/business/20161124/after_phone_image_6e0666b402fb90e
	 * [old_and_new_screnn_image] => /uploads/business/20161124/old_and_new_screnn_i
	 * [id_card_image] => /uploads/business/20161124/id_card_image_0463761e1f07a
	 * [repair_order_image] => /uploads/business/20161124/repair_order_imag
	 * [payable_image] => /uploads/business/20161124/payable_image_6e
	 * [service_note] => fsffw
	 * [m_order_id] => 420
	 * [m_order_service_id] => 2
	 * [_csrf-maintainer] => YVMuUFZzMl80Jk8bZTl1CFFlYQVgSkgtOTdBYTheRjs0BBkGJwVFbA==
	 *
	 * @note 处理商家理赔流程
	 */
	public function actionDolipei()
	{
		$m_order_id = Yii::$app->request->post('m_order_id', 0);
		$m_order_service_id = Yii::$app->request->post('m_order_service_id', 0);
		$service_status = Yii::$app->request->post('service_status', 0);
		$service_note = Yii::$app->request->post('service_note', '');//备注
		$province = Yii::$app->request->post('province_id', '');
		$city = Yii::$app->request->post('city_id', '');
		$area = Yii::$app->request->post('area_id', '');
		$detail_address = trim(Yii::$app->request->post('detail_address', ''));

		if (empty($province) || empty($city) || empty($area) || empty($detail_address)) {
			return $this->getCheckNo('请选择并填写报修地址');
		}
		$province_id = explode(',', $province)[0];
		$city_id = explode(',', $city)[0];
		$area_id = explode(',', $area)[0];
		$type = intval(Yii::$app->request->post('type', 0));
		if (!$type) {
			return $this->getCheckNo('不支持的维修类型');
		}
		$express_id = intval(Yii::$app->request->post('express_id', 0));
		$express_number = trim(Yii::$app->request->post('express_number', ''));


		$model_service = OrderMaintenanceService::findOne(['id' => $m_order_service_id, 'm_id' => $this->seller->seller_id, 'm_order_id' => $m_order_id]);
		if (empty($model_service)) {
			return $this->getCheckNo('查无指派记录');
		}

		$model_order = OrderMaintenance::findOne(['id' => $m_order_id]);

		$status = array_keys(OrderMaintenanceService::showSellerState());
		if (!in_array($service_status, $status)) {
			return $this->getCheckNo('无权操作此状态');
		}
		if (empty($service_note)) {
			return $this->getCheckNo('请填写简要备注信息');
		}
		//更新
		$model_order->type = $type;
		$model_order->express_id = $express_id;
		$model_order->express_number = $express_number;
		$model_order->province_id = $province_id;
		$model_order->city_id = $city_id;
		$model_order->area_id = $area_id;
		$model_order->address = $detail_address;


		//理赔资料提交处理
		if ($service_status == OrderMaintenanceService::_MS_STATE_INFO_TO_BE_SUBMIT) {
			$order_extend = OrderExtend::findOne(['order_id' => $model_order['order_id']]);
			if (empty($order_extend)) {
				return $this->getCheckNo('查无对应机型投保信息');
			}

			$hasOffer = MaintenanceOffer::checkSellerOffer($this->seller->seller_id, $order_extend['brand_id'], $order_extend['model_id'], Yii::$app->request->post('offer_info', 0));
			if (empty($hasOffer)) {
				return $this->getCheckNo('查无对应机型报价信息');
			}
			$root = Yii::getAlias('@webroot');
			$verfiyImageData = OrderMaintenanceService::$verfiyImage;
			foreach ($verfiyImageData as $key => &$val) {
				$file = Yii::$app->request->post($key, '');
				if (empty($file)) {
					return $this->getCheckNo('请上传' . $val['name']);
				}
				if (!is_readable($root . $file)) {
					return $this->getCheckNo('无效图片，请重新上传' . $val['name']);
				}
				$val['href'] = $file;
			}


			//print_r($verfiyImageData);die;
			/*if (!in_array(Yii::$app->request->post('damage_type', 0), array_keys(OrderMaintenanceService::baojiaType()))) {
				return $this->getCheckNo('请设置报价类型');
			}*/

			$m_price = Yii::$app->request->post('m_price',0);
			if (!is_numeric($m_price)) {
				return $this->getCheckNo('请填写整数维修报价或者保留2位小数');
			}


			$model_order->state = OrderMaintenance::_MT_STATE_SUCCESS;//完成

			$transaction = Yii::$app->db->beginTransaction();

			try {
				$model_order->update(false, ['type', 'express_id', 'express_number', 'province_id', 'city_id', 'area_id', 'address','state']);
				$model_service->setVerfiyImageInfo($verfiyImageData);
				$model_service->service_status = OrderMaintenanceService::_MS_STATE_TO_CHECK;
				$model_service->vertify_result = $service_note;
				//$model_service->damage_type = Yii::$app->request->post('damage_type', 0);
				$model_service->damage_type = OrderMaintenanceService::_PM_TYPE_ALL;
				//$model_service->inner_price = $hasOffer['inner_screen'];
				//$model_service->outer_price = $hasOffer['outer_screen'];

				//$model_service->expenses = $hasOffer['commission'];
				if ($model_service->damage_type == OrderMaintenanceService::_PM_TYPE_INNER) {
					//$model_service->total_price = $model_service->inner_price;
				} elseif ($model_service->damage_type == OrderMaintenanceService::_PM_TYPE_OUTER) {
					//$model_service->total_price = $model_service->outer_price;
				} elseif ($model_service->damage_type == OrderMaintenanceService::_PM_TYPE_ALL) {
					$model_service->total_price = $m_price;
				}

				$model_service->repair_ok_time = time();
				if ($model_service->update(false)) {
					$note = $model_service->getStatusText() . '#' . $service_note;
					OrderMaintenanceLog::addLog($model_service, $note);
					$transaction->commit();
					return $this->getCheckYes([], '处理成功');
				} else {
					$transaction->rollBack();
					return $this->getCheckNo('处理失败，请联系管理人员核查');
				}
			} catch (Exception $e) {
				$transaction->rollBack();
				return $this->getCheckNo('处理失败#' . $e->getMessage());
			}
			$transaction->rollBack();
			return $this->getCheckNo('处理失败#' . $e->getMessage());
		}

		//其他处理流程

		$transaction = Yii::$app->db->beginTransaction();
		try {
			$model_order->update(false, ['type', 'express_id', 'express_number', 'province_id', 'city_id', 'area_id', 'address']);
			if ($model_order->serviceLipei($model_service, $service_status, $service_note, 1)) {
				$transaction->commit();
				return $this->getCheckYes([], '处理成功');
			}
		} catch
		(Exception $e) {
			$transaction->rollBack();
			return $this->getCheckNo('处理失败#' . $e->getMessage());
		}
		$transaction->rollBack();
		return $this->getCheckNo('处理失败，请联系管理人员核查');
	}

}
