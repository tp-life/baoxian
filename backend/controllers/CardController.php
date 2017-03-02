<?php
/**
 * 卡券生成管理
 */

namespace backend\controllers;

use backend\components\LoginedController;
use common\library\helper;
use common\models\CardGrantRelation;
use common\models\CardOrderItem;
use common\models\CardOrderItemLog;
use common\models\CardOrderPayback;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\CardCouponsGrant;
use common\models\InsuranceCoverage;
use common\models\Seller;
use common\models\CardCouponsLog;
use m35\thecsv\theCsv;

/**
 * CardController implements the CRUD actions for CardCouponsGrant model.
 */
class CardController extends LoginedController
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [];
	}


	/**c
	 * Lists all CardCouponsGrant models.
	 * @return mixed
	 */
	public function actionIndex()
	{

		if (Yii::$app->request->isAjax) {
			$respon = array();
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量
			$status = Yii::$app->request->post('status', '');//激活状态
			$search_type = Yii::$app->request->post('search_type', 0);//搜索类型
			$keyword = trim(Yii::$app->request->post('keyword', ''));//搜索关键字
			$seller_id = intval(Yii::$app->request->post('seller_id', '0'));
			$conditon = array();
			$params = array();

			if ($status !== '') {
				$conditon['status'] = $status;
			}

			if ($search_type && $keyword) {
				if ($search_type == 1) { //卡券序号
					$conditon['card_number'] = $keyword;
				} elseif ($search_type == 2) { //险种
					$conditon['coverage_code'] = $keyword;
				}
			}
			if ($seller_id) {
				$conditon['seller_id'] = $seller_id;
			}

			$total = CardCouponsGrant::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => CardCouponsGrant::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$seller_info = Seller::getSellerInfo($item['seller_id']);
					$respon[] = [
						Html::checkbox('check_id[' . $item->id . ']'),
						$item->id,
						$item->card_number,
						$item->card_secret,
						$item->created,
						$item->coverage_code,
						$item->getStatusText(),
						$seller_info['seller_name'],
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		$ownerList = $this->getCardOwnerGroupList();
		$list_seller = ArrayHelper::map($ownerList, 'seller_id', 'seller_name');
		return $this->render('index', ['list_seller' => $list_seller]);

	}

	/**
	 * 获取 按商家搜索条件 商家列表
	 **/
	protected function getCardOwnerGroupList()
	{
		$query = new Query();
		$query->from(['a' => CardCouponsGrant::tableName()]);
		$query->leftJoin(['b' => Seller::tableName()], 'a.seller_id=b.seller_id');
		$query->select('a.seller_id,b.seller_name')->groupBy('a.seller_id');
		$data = $query->all();
		return $data;
	}

	/**
	 * Creates a new CardCouponsGrant model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$card_coupons_grant_model = new CardCouponsGrant();
		$insurance_coverage_model = new InsuranceCoverage();
		$insurance_coverage_data = $insurance_coverage_model::find()->select('id,type_id,type_name')->groupBy('type_id')->asArray()->all();

		if (Yii::$app->request->isPost) {
			$post_data = Yii::$app->request->post();
			if ($post_data['number'] > 1000) {
				$this->showMessage('生成条数不能大于1000', '操作提示', 'danger', Url::to(['card/create']));
			}
			if (!$post_data['type_id'] || !$post_data['company'] || !$post_data['coverage']) {
				$this->showMessage('请填写必填参数', '操作提示', 'danger', Url::to(['card/create']));
			}

			if ($this->_create_card($post_data)) {
				if ($post_data['is_export']) {
					$this->showMessage('卡券生成成功稍后自动导出', '操作提示', '', Url::to(['card/create', 'is_export' => 1, 'flag' => md5(date('Ymd'))]));
				} else {
					$this->showMessage('卡券生成成功', '操作提示', '', Url::to(['card/index']));
				}

			} else {
				$this->showMessage('生成卡券异常', '操作提示', 'danger', Url::to(['card/create']));
			}
		}

		if ($_REQUEST['is_export'] == 1 && $_REQUEST['flag'] == md5(date('Ymd'))) {
			$url = Url::to(['card/opene']);
			echo '<script>setTimeout(function(){window.open("' . $url . '");},800)</script>';
		}

		return $this->render('create', [
			'model' => $card_coupons_grant_model,
			'insurance_coverage_data' => $insurance_coverage_data,
		]);
	}

	public function actionOpene()
	{
		if ($export_data = Yii::$app->session->get('is_export_data')) {
			Yii::$app->session->remove('is_export_data');
			$export_data_real = [];
			foreach ($export_data as $_ex) {
				$export_data_real[] = [
					'="' . $_ex['card_number'] . '"',
					'="' . $_ex['card_secret'] . '"',
					$_ex['created'],
					$_ex['coverage_code'],
					'未激活',
					'乐换新'
				];
			}
			return $this->_export($export_data_real);
		} else {
			return $this->redirect(['card/create']);
		}
	}

	/**
	 * 获取保险公司列表
	 */
	public function actionCompany()
	{
		$type_id = Yii::$app->request->post('type_id');

		if (!$type_id) {
			return $this->getCheckNo('参数错误');
		}

		$company_list = InsuranceCoverage::find()->select('id,company_id,company_name')->where(['type_id' => $type_id])->groupBy('company_id')->asArray()->all();

		return $this->getCheckYes($company_list);
	}

	/**
	 * 获取保险公司列表
	 */
	public function actionCoveragelist()
	{
		$company_id = Yii::$app->request->post('company_id');

		if (!$company_id) {
			return $this->getCheckNo('参数错误');
		}

		$coverage_list = InsuranceCoverage::find()->select('id,coverage_code')->where(['company_id' => $company_id])->asArray()->all();

		return $this->getCheckYes($coverage_list);
	}

	public function actionCknum()
	{
		$number = Yii::$app->request->post('number');
		if (!$number) {
			return $this->getCheckNo('参数错误');
		}
		$f_abc = $this->getCardNumber($number);
		if (is_array($f_abc)) {
			$d = array();
			foreach ($f_abc as $v) {
				if ($v[1]) {
					$v_x = str_pad($v[2], 3, 0, STR_PAD_LEFT);
					$d[] = "开始批号:<b>{$v[0]}</b>&nbsp;序号:<b>{$v_x}</b>&nbsp;数量：<b>{$v[1]}</b>";
				}

			}
			$d = implode('<br/>', $d);

			return $this->getCheckYes($d);
		}
		return $this->getCheckNo('系统错误');
	}

	private function getCardNumber($number = 1000)
	{
		$has = CardCouponsGrant::find()->where(['card_number' => '0001000'])->asArray()->one();
		$prefix = '0001';

		if (empty($has)) {
			return array(
				array($prefix, $number, 0)
			);
		}

		$data = CardCouponsGrant::find()->where('LENGTH(card_number)=7')->andWhere(['NOT REGEXP ', 'card_number', '[a-z]+'])->orderBy('id desc')->asArray()->one();
		if ($data) {
			$card_number = $data['card_number'];
			$prefix_4 = substr($card_number, 0, 4);//前4位
			$suffix_3 = substr($card_number, 4);//后3位

			$start_number = ltrim($prefix_4, 0);
			$now_number = ltrim($suffix_3, 0);
			if ($now_number + $number < 1000) {
				return array(
					array($prefix_4, $number, $now_number + 1)
				);
			}
			$sub_number = $now_number + $number - 1000 + 1;
			$start_number_new = $start_number + 1;
			$patter_1 = array(
				$prefix_4,
				1000 - $now_number - 1,
				$now_number + 1
			);
			$patter_2 = array(
				str_pad($start_number_new, 4, 0, STR_PAD_LEFT),
				$sub_number,
				0
			);
			return array($patter_1, $patter_2);
		}

		return '';
	}

	private function _create_card($post_data)
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		/**
		 * [d_type] => 2
		 * [d_company] => 1
		 * [d_coverage] => 3#LX0350002
		 * [number] => 100*/
		$time = date('Y-m-d H:i:s', time());
		$d_type = intval($post_data['type_id']);
		$d_company = intval($post_data['company']);
		$number = intval($post_data['number']);
		$d_coverage = explode('#', trim($post_data['coverage']));
		$coverage_id = $d_coverage[0];
		$coverage_code = $d_coverage[1];
		$f_abc = $this->getCardNumber($number);

		$uu = array();
		while (count($uu) < $number) {
			foreach ($f_abc as $v) {
				$t_number = $v[1];
				$t_start = $v[2];
				for ($i = $v[2]; $i < $t_number + $t_start; $i++) {
					$n = $v[0] . str_pad($i, 3, 0, STR_PAD_LEFT);
					$p = $this->createP();
					$d = array(
						'card_number' => $n,
						'card_secret' => $p,
						'seller_id' => 1,//乐换新商家账号
						'coverage_id' => $coverage_id,
						'coverage_code' => $coverage_code,
						'type_id' => $d_type,
						'company_id' => $d_company,
						'created' => $time
					);
					$uu[$n] = $d;

				}
			}
		}
		//echo '<pre/>';print_r($uu);die;
		sort($uu);
		//echo '<pre/>';print_r($uu);die;
		$card_coupons_grant_model = new CardCouponsGrant();
		$flag = false;
		try {
			$transaction = $card_coupons_grant_model->getDb()->beginTransaction();
			if (count($uu) > 800) {
				$ck_uu = array_chunk($uu, 500);
			} elseif (count($uu) > 500) {
				$ck_uu = array_chunk($uu, 300);
			} else {
				$ck_uu = [$uu];
			}
			foreach ($ck_uu as $pp_i) {
				$pv = Yii::$app->db->createCommand()->batchInsert(CardCouponsGrant::tableName(), ['card_number', 'card_secret', 'seller_id', 'coverage_id',
					'coverage_code', 'type_id', 'company_id', 'created'], $pp_i)->execute();
				if (!$pv) {
					throw  new Exception('error');
				}
			}
			$transaction->commit();
			$flag = true;
		} catch (Exception $e) {
			$this->log($e->getFile());
			$this->log($e->getLine());
			$this->log($e->getCode());
			$this->log($e->getMessage());
			$transaction->rollback();
		}
		if ($flag && $post_data['is_export']) {
			Yii::$app->session->set('is_export_data', $uu);
		}
		return $flag;
	}

	protected function createP()
	{
		$mo = date('m');
		while (true) {
			usleep(20);
			$r = mt_rand(0, 9);
			$num = mt_rand(0, 999);
			$number_2 = mt_rand(10, 99) . $mo;
			$number_7 = substr(str_pad($num, 5, $r), 0, 5);
			$number_3 = mt_rand(100, 999);
			$p = $number_2 . $number_7 . $number_3;
			if (CardCouponsGrant::hasCardSecret($p)) {
				usleep(10);
				continue;
			}
			break;
		}
		return $p;
	}

	/**
	 * 卡券合并
	 */
	public function actionMerge()
	{

		if (Yii::$app->request->isPost) {
			$from_seller_id = Yii::$app->request->post('from_seller_id');
			$to_seller_id = Yii::$app->request->post('to_seller_id');
			$d_coverage = Yii::$app->request->post('d_coverage');
			$card_number_str = Yii::$app->request->post('card_number_str');
			$card_number_str = trim($card_number_str, ',');

			if (!$from_seller_id || !$to_seller_id || !$d_coverage || !$card_number_str) {
				$this->showMessage('参数错误', '操作提示', __MSG_DANGER, Url::to(['/card/merge']));
			}

			$con = [
				'seller_id' => $from_seller_id,
				'status' => 0,
				'coverage_code' => $d_coverage,
				'card_number' => explode(',', $card_number_str)
			];
			$count = CardCouponsGrant::find()->where($con)->count('id');

			if ($count == 0) {
				$this->showMessage('被合并商家未激活卡券数量为0,合并失败', '操作提示', __MSG_DANGER, Url::to(['/card/merge']));
			}
			$list = CardCouponsGrant::find()->where($con)->asArray()->all();

			$time = date('Y-m-d H:i:s', time());
			$str = '';
			$id_s = '';
			$transaction = Yii::$app->getDb()->beginTransaction();
			foreach ($list as $v) {
				$id_s .= $v['id'] . ',';
				$msg = '合并险种(' . $v['coverage_code'] . ') (' . $v['card_number'] . ')从[' . $from_seller_id . '] 到商家[' . $to_seller_id . ']';

				$str .= '(3,' . $from_seller_id . ',' . $to_seller_id . ',"' . $msg . '","' . $time . '"),';
			}
			$sql = 'INSERT INTO ' . CardCouponsLog::tableName() . ' (`hand_type`,`from_seller_id`,`to_seller_id`,`message`,`created`) VALUES' . trim($str, ',');
			$ret = Yii::$app->getDb()->createCommand($sql)->execute();

			$id_s = trim($id_s, ',');
			$sql2 = 'update ' . CardCouponsGrant::tableName() . ' set seller_id = ' . $to_seller_id . ' where id in(' . $id_s . ')';
			$ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();

			if ($ret && $ret2) {
				$transaction->commit();
				$this->showMessage('合并成功', '操作提示', __MSG_DANGER, Url::to(['/card/merge']));
			} else {
				$transaction->rollBack();
				$this->showMessage('合并失败', '操作提示', __MSG_DANGER, Url::to(['/card/merge']));
			}
			return $this->render('/card/merge');
		}

		$insurance_list = Seller::getSellerList('insurance');

		return $this->render('merge', [
			'insurance_list' => $insurance_list
		]);
	}

	/**
	 * JAAX获取商家的险种列表
	 */
	public function actionGetcoveragelist()
	{
		$seller_id = Yii::$app->request->post('seller_id');
		if (!$seller_id) {
			return $this->getCheckNo('参数错误!');
		}

		$list = CardCouponsGrant::getCoverageCodeList($seller_id);

		return $this->getCheckYes($list);
	}

	/**
	 * AJAX获取商家列表
	 * seller_name 商家名称
	 * type 商家类型
	 */
	public function actionGetsellerlist()
	{
		$seller_name = Yii::$app->request->post('seller_name');
		$type = Yii::$app->request->post('type');

		if (!$seller_name || !$type) {
			return $this->getCheckNo('参数错误!');
		}

		$newType = 'is_' . $type;
		$map = [
			$newType => 1,
			'status' => 1
			//'seller_name' => ['like',$seller_name],
		];
		$list = Seller::find()
			->where($map)
			->andWhere(['like', 'seller_name', $seller_name])
			->asArray()
			->all();

		return $this->getCheckYes($list);
	}


	/**
	 * @leo.yan 优化
	 * 数据导出
	 * Array
	 * (
	 * [seller_id]=>
	 * [status] =>
	 * [search_type] =>
	 * [keyword] =>
	 * [id_all] => on
	 * [check_id] => Array
	 * (
	 * [975] => 1
	 * [974] => 1
	 * [973] => 1
	 * ....
	 * ....
	 * ....
	 * [926] => 1
	 * )
	 *
	 * [datatable_list_length] => 50
	 * )
	 */
	public function actionExport()
	{
		//echo '<pre>';
		//print_r($_GET);die;
		$seller_id = intval(Yii::$app->request->get('seller_id', 0));
		//$pageSize = intval(Yii::$app->request->get('datatable_list_length', 10));//当前页pageSize
		$status = trim(Yii::$app->request->get('status', ''));//激活状态
		$search_type = Yii::$app->request->get('search_type', 0);//搜索类型
		$keyword = trim(Yii::$app->request->get('keyword', ''));//搜索关键字
		$is_all = (isset($_REQUEST['id_all']) && $_REQUEST['id_all'] == 'on') ? true : false;
		$id_list = array();
		if (!$is_all) {
			$check_id_list = $_REQUEST['check_id'];
			if ($check_id_list) {
				foreach ($check_id_list as $k_id => $v_is_ok) {
					if ($v_is_ok) {
						$id_list[] = $k_id;
					}
				}
			}

		}
		$conditon = array();

		if ($status !== '') {
			$conditon['status'] = $status;
		}

		if ($search_type && $keyword) {
			if ($search_type == 1) { //卡券序号
				$conditon['card_number'] = $keyword;
			} elseif ($search_type == 2) { //险种
				$conditon['coverage_code'] = $keyword;
			}
		}
		if (!$is_all && $id_list) {
			$conditon['id'] = $id_list;
		}
		if ($seller_id) {
			$conditon['seller_id'] = $seller_id;
		}
		$query = CardCouponsGrant::find()->where($conditon);
		$total = $query->count('id');
		if ($total > 5000) {
			$this->showMessage('导出卡券已超过5000张限制', '警告提示', 'danger', 'javascript:window.close();');
		}
		//print_r($conditon);die;
		$data = $query->orderBy('id DESC')->limit(5000)->all();

		foreach ($data as $item) {
			$seller_info = Seller::getSellerInfo($item->seller_id);
			$respon[] = [
				'="' . $item->card_number . '"',
				'="' . $item->card_secret . '"',
				$item->created,
				$item->coverage_code,
				$item->getStatusText(),
				$seller_info['seller_name'],
			];
		}
		set_time_limit(0);
		$this->_export($respon);
	}

	public function actionExportdata()
	{
		$_t = Yii::$app->request->get('_t', 0);
		$pathfile = Yii::getAlias('@runtime') . "/card_list_" . date('Y_m_d_H', time()) . "_{$_t}.csv";

		if (is_readable($pathfile)) {
			$txt = file_get_contents($pathfile);
			unlink($pathfile);
			Yii::$app->response->sendContentAsFile($txt, "card_list_" . date('Y_m_d_H', time()) . "_{$_t}.csv");
			//Yii::$app->response->sendFile($pathfile);
		} else {
			die('over');
		}
	}

	//把一个数组分成几个数组
	//$arr 是数组
	//$num 是数组的个数
	public function partition($arr, $num)
	{
		//数组的个数
		$listcount = count($arr);
		//分成$num 个数组每个数组是多少个元素
		$parem = floor($listcount / $num);
		//分成$num 个数组还余多少个元素
		$paremm = $listcount % $num;
		$start = 0;
		for ($i = 0; $i < $num; $i++) {
			$end = $i < $paremm ? $parem + 1 : $parem;
			$newarray[$i] = array_slice($arr, $start, $end);
			$start = $start + $end;
		}
		return $newarray;
	}

	private function _export($respon)
	{
		theCsv::export([
			'data' => $respon,
			'name' => "card_list_" . date('Y_m_d_H', time()) . ".csv",    // 自定义导出文件名称
			'header' => ['卡券序列号', '卡券密匙', '生成时间', '险种', '卡券状态', '所属商家'],
			//'header' => ['卡券序列号','生成时间','险种','卡券状态','所属商家'],
		]);
	}

	/**
	 * 卡券申请列表
	 * @return string
	 */
	public function actionIssue()
	{
		if (Yii::$app->request->isAjax) {

			$respon = array();
			$query = CardOrderItem::find()->from(['a' => CardOrderItem::tableName()]);
			$query->leftJoin(['b' => CardOrderPayback::tableName()], 'a.pay_sn=b.pay_sn');
			$query->leftJoin(['c' => InsuranceCoverage::tableName()], 'a.coverage_code=c.coverage_code');
			$query->andWhere(['b.from_seller_id' => Seller::$lehuanxin]);

			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量

			$status = trim(Yii::$app->request->post('status', ''));
			if ($status !== '') {
				$query->andWhere(['a.status' => intval($status)]);
			}
			if ($coverage_code = Yii::$app->request->post('coverage_code', '')) {
				$query->andWhere(['a.coverage_code' => $coverage_code]);
			}
			if ($pay_sn = Yii::$app->request->post('pay_sn', '')) {
				$query->andWhere(['a.pay_sn' => $pay_sn]);
			}
			$apply_type = trim(Yii::$app->request->post('apply_type', ''));
			if ($apply_type !== '') {
				$query->andWhere(['b.apply_type' => intval($apply_type)]);
			}
			$pay_status = trim(Yii::$app->request->post('pay_status', ''));
			if ($pay_status !== '') {
				$query->andWhere(['b.pay_status' => intval($pay_status)]);
			}

			$total = $query->count('a.order_id');
			$data = $query->select('a.*,b.apply_type,b.pay_status,b.handle_type,b.to_seller_id,c.company_name,c.type_name,c.coverage_name')->orderBy('a.order_id DESC')->limit($pageSize)->offset($start)->asArray()->all();

			if ($data) {
				foreach ($data as $item) {
					$t_seller_name = Seller::getSellerInfo($item['to_seller_id'])->seller_name;
					$btn = '<a class="btn green btn-xs  btn-default" data-target="#my-card-apply" data-toggle="modal"  href="' . $this->createUrl(['card/info', 'pay_sn' => $item['pay_sn']]) . '"><i class="fa fa-share"></i> 查看详细</a>';
					//没有 确认和发放处理的可以发放和取消操作
					if ($item['status'] == CardOrderItem::_CD_STATE_TO_DO || $item['status'] == CardOrderItem::_CD_STATE_TO_WAIT) {
						$btn .= '<a class="btn red btn-xs btn-default apply_cancel" data-content="您确定要取消 [' . $t_seller_name . '] 的险种 [' . $item['coverage_code'] . '] 发放？" rel="' . $this->createUrl(['card/cancel', 'order_id' => $item['order_id']]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 取消 </a>';
						$btn .= '<a class="btn default btn-default btn-xs" data-target="#my-card-apply" data-toggle="modal"    href="' . $this->createUrl(['card/issuemod', 'order_id' => $item['order_id']]) . '"> 发放 </a>';
					}
					$respon[] = [
						$t_seller_name,
						$item['company_name'] . ' ' . $item['type_name'] . ' ' . $item['coverage_name'],
						$item['coverage_code'],
						$item['number'],
						$item['price'],
						$item['pay_sn'],
						!$item['handle_type'] ? '' : CardOrderPayback::getMsg($item['apply_type']),
						CardOrderPayback::getTypeMsg($item['pay_status']),
						CardOrderItem::itemStateData()[$item['status']],
						$item['send_time'] ? date('Y-m-d H:i', $item['send_time']) : '',
						$btn
					];
				}
			}

			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}


		return $this->render('issue');
	}

	public function actionInfo()
	{
		if (!Yii::$app->request->isAjax) {
			$this->showMessage('非法请求，请联系管理员', '', self::__MSG_DANGER);
		}
		$model = CardOrderPayback::findOne(['pay_sn' => trim($_REQUEST['pay_sn'])]);
		if (!$model) {
			$this->showMessage('查无批次记录，请联系管理员', '', self::__MSG_DANGER);
		}
		return $this->renderPartial('_card_view', ['model' => $model]);
	}

	/**
	 * 被动发放卡券   平台与1级商家之间
	 * @return array
	 */
	public function actionIssuemod()
	{

		if (Yii::$app->request->isPost) {
			/**
			 * [card_number_str] => fgh
			 * [service_note] => fh
			 * [order_id] => 59
			 * [_csrf-maintainer] => T2VIY1NkLjcoCQ0sAytXfSgyBSIHPHxCJhUqO2MBF0Y7PXsEEFxpfQ==
			 */
			set_time_limit(0);
			$order = CardOrderItem::findOne(['order_id' => intval($_REQUEST['order_id'])]);
			if (!$order) {
				return $this->getCheckNo('查无申领记录');
			}
			$card_pay = CardOrderPayback::findOne(['pay_sn' => $order->pay_sn, 'from_seller_id' => Seller::$lehuanxin]);
			if (!$card_pay) {
				return $this->getCheckNo('查无申领记录.');
			}

			//check card
			$cards = explode(',', trim($_REQUEST['card_number_str']));
			if (count($cards) != $order->number) {
				return $this->getCheckNo('申领卡券数量与发放数据不符合.');
			}
			$service_note = trim($_REQUEST['service_note']);
			$deadline = intval($_REQUEST['deadline']);

			$card_data = CardCouponsGrant::find()->where(['seller_id' => $card_pay['from_seller_id'], 'status' => CardCouponsGrant::__STATUS_DEFAULT, 'coverage_code' => $order['coverage_code'], 'card_number' => $cards])->asArray()->all();

			if (count($card_data) != count($cards)) {
				return $this->getCheckNo('商家可发放卡券数量不足');
			}

			$time = time();
			$insert_data = array();
			foreach ($card_data as $card) {
				$insert_data[] = [
					$card['id'],
					$card['card_number'],
					$order['order_id'],
					$order['pay_sn'],
					$card_pay['from_seller_id'],
					$card_pay['to_seller_id'],
					$time,
					$deadline ? strtotime("+ $deadline days") : 0
				];
			}
			$transtion = Yii::$app->db->beginTransaction();
			try {
				//改变所属
				$flag = CardCouponsGrant::updateAll(['seller_id' => $card_pay['to_seller_id'], 'status' => 0], 'card_number in(' . trim(implode(',', $cards), ',') . ')');
				//添加关系

				$f1 = Yii::$app->db->createCommand()->batchInsert(CardGrantRelation::tableName(), ['card_id', 'card_number', 'order_id', 'pay_sn', 'from_seller_id', 'to_seller_id', 'add_time', 'deadline'], $insert_data)->execute();
				//添加卡券日志
				$c_log = [
					'hand_type' => CardCouponsLog::__TYPE_GRANT,
					'from_seller_id' => $card_pay['from_seller_id'],
					'to_seller_id' => $card_pay['to_seller_id'],
					'message' => '发放险种(' . $order['coverage_code'] . ') (' . $order['number'] . ')从[' . $card_pay['from_seller_id'] . '] 到商家[' . $card_pay['to_seller_id'] . ']',
					'created' => date('Y-m-d H:i:s', $time)
				];
				CardCouponsLog::addLog($c_log);
				$order->status = CardOrderItem::_CD_STATE_SUCCESS;
				//$order->add_time = $time;
				$order->send_time = $time;
				$f2 = $order->update(false, ['status', 'send_time']);
				$card_pay->send_total_price += $order->price * $order->number;
				$card_pay->add_time = $time;
				$f3 = $card_pay->update(false, ['send_total_price', 'add_time']);

				CardOrderItemLog::addLog(['order_id' => $order->order_id, 'content' => $service_note]);
				if ($flag && $f1 && $f2 && $f3) {
					$transtion->commit();
					return $this->getCheckYes('卡券发放成功！');
				} else {
					$transtion->rollBack();
					return $this->getCheckNo('卡券发放失败');
				}

			} catch (Exception $e) {
				$transtion->rollBack();
				return $this->getCheckNo('卡券发放异常#' . $e->getMessage());
			}
		}

		//check order
		$order = CardOrderItem::findOne(['order_id' => intval($_REQUEST['order_id'])]);
		if (!$order) {
			$this->showMessage('查无申领险种记录', '', self::__MSG_DANGER);
		}
		//check pay
		$card_pay = CardOrderPayback::findOne(['pay_sn' => $order->pay_sn, 'from_seller_id' => Seller::$lehuanxin]);
		if (!$card_pay) {
			$this->showMessage('查无申领险种记录', '', self::__MSG_DANGER);
		}
		$seller = Seller::findOne(['seller_id' => $card_pay['to_seller_id']]);

		return $this->renderPartial('_card_send', ['order' => $order, 'card_pay' => $card_pay, 'seller' => $seller]);

	}

	/**
	 * 取消险种发放
	 * @return array
	 */
	public function actionCancel()
	{
		if (!Yii::$app->request->isAjax) {
			return $this->getCheckNo('非法请求');
		}

		//check order
		$order = CardOrderItem::findOne(['order_id' => intval($_REQUEST['order_id'])]);
		if (!$order) {
			return $this->getCheckNo('查无申领记录');
		}
		//check pay

		$card_pay = CardOrderPayback::findOne(['pay_sn' => $order->pay_sn, 'from_seller_id' => Seller::$lehuanxin]);

		if (!$card_pay) {
			return $this->getCheckNo('查无申领险种记录');
		}
		if ($order->cancelApply($card_pay)) {
			return $this->getCheckYes([], '取消成功');
		}
		return $this->getCheckNo('取消失败');
	}


	/**
	 * 主动发放卡券
	 * @param array $condition
	 * @return \yii\db\ActiveQuery
	 */
	public function actionAccrod()
	{
		$seller_id = 1;
		$insurance_list = Seller::find()->where(['pid' => 0, 'is_insurance' => 1, 'status' => 1])->andWhere(['<>', 'seller_id', $seller_id])->all();
		$list = CardCouponsGrant::getCoverageCodeList($seller_id);
		return $this->render('accrod', [
			'insurance_list' => $insurance_list,
			'code_list' => $list
		]);
	}

	/**
	 * 主动发放卡券  须优化
	 **/
	public function actionGrant()
	{
		if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
			$this->showMessage('非法访问');
		}
		$post = Yii::$app->request->post();
		if (!$post['to_seller_id'] || !$post['d_coverage'] || !$post['card_number_str'] || !$post['card_num'] || $post['apply_type'] === '') {
			return $this->getCheckNo(' 参数错误');
		}
		$coverage = InsuranceCoverage::findOne(['coverage_code' => $post['d_coverage']]);
		if (!$coverage) return $this->getCheckNo('当前险种不存在！');
		$transaction = Yii::$app->getDb()->beginTransaction();
		try {
			$data = [
				'apply_type' => intval($_REQUEST['apply_type']),
				'from_seller_id' => 1,
				'to_seller_id' => $post['to_seller_id'],
				'num' => $post['card_num'],
				'total_price' => floatval($coverage->wholesale_price * $post['card_num']),
				'coverage_code' => $post['d_coverage'],
				'price' => $coverage->wholesale_price
			];
			set_time_limit(0);
			//生成卡号 支持以逗号分割单个与 | 分割连续的卡号
			$cards = helper::creadCard($post['card_number_str']);
			if (!$cards) {
				throw new Exception('卡券号输入错误');
			}
			//创建领用订单
			$b = CardOrderPayback::create($data, Yii::$app->user->identity->id);
			if (!$b) {
				throw new Exception('订单创建失败！');
			}
			$t = $post['card_deadline'] ? $post['card_deadline'] : 0;
			//验证卡券号是否与申请数相等
			$check_number = CardOrderItem::checkNumber($b, $post['d_coverage'], $cards);
			if (!$check_number['status']) {
				throw new Exception($check_number['msg']);
			}
			$check_number['data']['content'] = $post['card_remark'];
			$check_number['data']['t'] = $t;
			$tj = ['seller_id' => 1, 'coverage_code' => $post['d_coverage'], 'status' => 0, 'card_number' => $cards];
			$bstop = CardCouponsGrant::_merage($tj, $check_number['data']);
			if ($bstop) {
				$transaction->commit();
				return $this->getCheckYes([], '卡券发放成功！');
			}
			throw new Exception('卡券发放失败');
		} catch (Exception $e) {
			$transaction->rollBack();
			return $this->getCheckNo($e->getMessage());
		}
	}

	/**
	 * AJAX获取商家列表
	 * seller_name 商家名称
	 * type 商家类型
	 */
	public function actionGetseller()
	{
		$seller_name = Yii::$app->request->post('seller_name');
		$type = Yii::$app->request->post('type');

		if (!$type) {
			return $this->getCheckNo('参数错误!');
		}

		$newType = 'is_' . $type;
		$map = [
			$newType => 1,
			'status' => 1
		];
		$model = Seller::find()->where($map);
		if ($seller_name) {
			$model->andWhere(['like', 'seller_name', $seller_name]);
		}
		$model->andWhere(['pid' => 0]);//二级商家不能搜索出来
		$list = $model->andWhere(['<>', 'seller_id', 1])->asArray()->all();
		if (!$list) {
			return $this->getCheckNo('查无匹配记录#' . strip_tags($seller_name));
		}
		return $this->getCheckYes($list);
	}

	private function _condition($condition = [])
	{
		$model = CardOrderPayback::find();
		$where = ['from_seller_id' => 1];
		$post = Yii::$app->request->post();
		if (isset($post['status']) && $post['status'] !== '') {
			$where['pay_status'] = (int)$post['status'];
		}
		if (isset($post['keyword']) && $post['keyword']) {
			$child = Seller::find()->select('seller_id')->where(['like', 'seller_name', $post['keyword']])->asArray()->all();
			if ($child) {
				$to_seller_id = array_column($child, 'seller_id');
				$where['to_seller_id'] = $to_seller_id;
			} else {
				$where['to_seller_id'] = -1;
			}
		}
		$condition = array_merge($where, $condition);
		$model->where($condition);
		return $model;
	}


}
