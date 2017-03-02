<?php
namespace maintainer\controllers;

use common\models\BrandModel;
use common\models\CardCouponsGrant;
use common\models\InsuranceCoverage;
use common\models\Order;
use common\models\OrderExtend;
use common\models\OrderMaintenance;
use common\models\Seller;
use Yii;
use maintainer\components\LoginedController;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * 商户保险订单  只读
 */
class BorderController extends LoginedController
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
		$isRankTwo = $this->seller->isRankTwo;

		if (Yii::$app->request->isAjax) {
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量
			$query = new Query();
			$query->select('o.order_id,o.order_sn,o.order_state,o.coverage_code,o.add_time,o_e.buyer,o_e.buyer_phone,o_e.imei_code,o_e.brand_id,o_e.model_id,o_e.end_time,o_e.seller_name,g.card_number');
			$query->from(['o' => Order::tableName()]);
			$query->leftJoin(['o_e' => OrderExtend::tableName()], 'o.order_id = o_e.order_id');
			$query->leftJoin(['g' => CardCouponsGrant::tableName()], 'o.order_id=g.order_id and g.order_id!=0');
			if ($isRankTwo) {
				$query->where('o_e.seller_id=:seller_id', [':seller_id' => $this->seller->seller_id]);
				//$query->where('o_e.seller_id=:seller_id',[':seller_id'=>1]);//test
			} else {
				$sub_query = (new Query())->from(Seller::tableName())->select('seller_id')->where(['is_insurance' => 1, 'pid' => $this->seller->seller_id]);
				$query->where('o_e.seller_id=:seller_id', [':seller_id' => $this->seller->seller_id]);
				$query->orWhere(['o_e.seller_id' => $sub_query]);
				//$query->where('o_e.seller_id=:seller_id',[':seller_id'=>11]);
				//$query->orWhere(['o_e.seller_id'=>11]);//test sub query
			}
			if ($seller_id = intval(Yii::$app->request->post('seller_id', 0))) {
				$query->andWhere(['o_e.seller_id' => $seller_id]);
			}
			if ($add_time_from = Yii::$app->request->post('add_time_from', '')) {
				$query->andFilterCompare('o.add_time', strtotime($add_time_from), '>=');
			}
			if ($add_time_to = Yii::$app->request->post('add_time_to', '')) {
				$add_time_to = $add_time_to . ' 23:59:59';
				$query->andFilterCompare('o.add_time', strtotime($add_time_to), '<=');
			}
			if ($order_sn = trim(Yii::$app->request->post('order_sn', ''))) {
				$query->andWhere('o.order_sn =:order_sn', [':order_sn' => $order_sn]);
			}

			if ($buyer = trim(Yii::$app->request->post('buyer', ''))) {
				$query->andWhere(['like', 'o_e.buyer', $buyer]);
			}
			if ($buyer_phone = trim(Yii::$app->request->post('buyer_phone', ''))) {
				if (preg_match('/1[356789][0-9]{9}/', $buyer_phone)) {
					$query->andWhere('o_e.buyer_phone =:buyer_phone', [':buyer_phone' => $buyer_phone]);
				}
			}
			if ($imei_code = trim(Yii::$app->request->post('imei_code', ''))) {
				$query->andWhere('o_e.imei_code =:imei_code', [':imei_code' => $imei_code]);
			}
			if ($coverage_code = trim(Yii::$app->request->post('coverage_code', ''))) {
				$query->andWhere('o.coverage_code =:coverage_code', [':coverage_code' => $coverage_code]);
			}
			if ($seller_name = trim(Yii::$app->request->post('seller_name', ''))) {
				$query->andWhere(['like', 'o_e.seller_name', $seller_name]);
			}
			if ($card_number = trim(Yii::$app->request->post('card_number', ''))) {
				$query->andWhere('g.card_number =:card_number', [':card_number' => $card_number]);
			}
			$status = trim(Yii::$app->request->post('status', ''));
			if ($status !== '') {
				if ($status == 32) {//过保处理
					$query->andWhere(['<', 'o_e.end_time', time()]);
					$query->andWhere(['<>', 'o_e.end_time', 0]);
				} else {
					$query->andWhere(['order_state' => (int)$status]);
				}
			}
			$total = $query->count('o.order_id');
			$respon = $data = [];
			$data = $query->orderBy('o.order_id DESC')->limit($pageSize)->offset($start)->all();
			$order = new Order();

			if ($data) {
				foreach ($data as $item) {
					$brand = BrandModel::getInfo($item['brand_id']);
					$model = BrandModel::getInfo($item['model_id']);
					$name = $brand ? $brand['model_name'] . ' ' : '';
					$name .= $model ? $model['model_name'] : '';
					$btn = '<a class="btn green btn-xs  btn-default" target="_blank" title="点击查看详细" href="' . $this->createUrl(['border/view', 'id' => $item['order_id']]) . '"><i class="fa fa-share"></i> 查看详细</a>';
					$respon[] = [
						Html::a($item['order_sn'], ['border/view', 'id' => $item['order_id']], ['target' => '_blank', 'title' => '订单详情']),
						$item['buyer'],
						$item['buyer_phone'],
						$item['imei_code'],
						$name,
						$item['coverage_code'],
						$item['card_number'],
						'<span class="font-purple-seance">' . $order->getStatus($item) . '</span>',
						$item['seller_name'],
						$item['add_time'] ? date('Y-m-d H:i', $item['add_time']) : '',
						$btn,
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}



		$d = [$this->seller->seller_id => $this->seller->seller_name];
		//$seller_data  = Seller::find()->where(['pid'=>11])->select('seller_id,seller_name')->asArray()->all();
		if (!$isRankTwo) {
			$seller_data = Seller::find()->where(['pid' => $this->seller->seller_id])->select('seller_id,seller_name')->asArray()->all();
			if ($seller_data) {
				$d = ArrayHelper::map($seller_data, 'seller_id', 'seller_name');
			}
		}

		return $this->render('index', ['coverage_data' => InsuranceCoverage::getCoverageDataCodeAll(), 'seller_data' => $d]);
	}

	/**
	 * Displays a single Order model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		$query = new Query();
		$order_info = $query->from(['o' => Order::tableName(), 'o_e' => OrderExtend::tableName()])->where('o.order_id = o_e.order_id')
			->andWhere(['o.order_id' => $id])->one();
		$converage = InsuranceCoverage::find()->where(['id' => $order_info['coverage_id']])->one();
		$brand = new BrandModel();
		$brand_model = $brand->getBrand($order_info['brand_id'])->model_name . '#' .
			$brand->getBrand($order_info['model_id'])->model_name;
		$order_model = new Order();


		return $this->render('view', [
			'order' => $order_info,
			'coverage' => $converage,
			'brand' => $brand_model,
			'status' => $order_model->getStatus($order_info)
		]);
	}


}
