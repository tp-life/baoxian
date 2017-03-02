<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\library\helper;
use common\models\Area;
use common\models\Bank;
use common\models\BrandModel;
use common\models\CardCouponsGrant;
use common\models\CardGrantRelation;
use common\models\Member;
use m35\thecsv\theCsv;
use Yii;
use common\models\Seller;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SellerController implements the CRUD actions for Seller model.
 */
class SellerController extends LoginedController
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
		if (Yii::$app->request->isAjax) {
			//子商家所属商户
			if (Yii::$app->request->post('leader', '') == 'yes') {
				$sellerData = Seller::find()->select('seller_id,seller_name')->asArray()->all();
				return $this->getCheckYes($sellerData);
			}
			//商家信息
			if (Yii::$app->request->post('leader', '') == 'yes_and_info') {
				$sellerData = Seller::find()->select('*')->where(['seller_id'=>intval($_REQUEST['seller_id'])])->asArray()->one();
				return $this->getCheckYes($sellerData);
			}


			//datalist
			return $this->getListData();
		}

		$area = Area::findAll(['area_parent_id' => 0]);
		return $this->render('index', ['province' => $area]);
	}

	/**
	 * Array
	 * (
	 * [draw] => 3
	 * [start] => 0
	 * [length] => 10
	 * [search] => Array
	 * (
	 * [value] =>
	 * [regex] => false
	 * )
	 *
	 * [_csrf-backend] => Z2QwVUM3TFheVGIzFmQKKgY1Wh4OWy0SVCBYOCldBj4JNVxnB0QJKw==
	 * [province_id] => 2, 天津
	 * [city_id] => 40, 天津市
	 * [area_id] => 57, 河西区
	 * [seller_type] => 1                     //商家类型
	 * [status] => 2                        //合作状态
	 * [p_p] => 1                            //商家等级
	 * [p_s_id] => 5                        //子商户所属上级商家
	 * [filter_search_key] => concat_tel    //搜索key
	 * [filter] => retet                    //搜索world
	 * [action] => filter
	 * @leo.yan 数据优化及需求处理
	 * )*/

	protected function getAddressId($name)
	{
		$value = '';
		if (Yii::$app->request->isPost) {
			$value = Yii::$app->request->post($name, '');
		} else {
			$value = Yii::$app->request->get($name, '');
		}
		if (stripos($value, ',') !== false) {
			$value = explode($value, ',')[0];
		}
		return intval($value);
	}

	protected function getSearchedInfo()
	{
		if (Yii::$app->request->isPost) {
			$filed = Yii::$app->request->post('filter', '');
			$filed_key = trim(Yii::$app->request->post('filter_search_key', ''));
			$val = Yii::$app->request->post('status', '');
			$p_p = Yii::$app->request->post('p_p', '');
			$pre_leader_seller_id = Yii::$app->request->post('p_s_id', '');
			$seller_type_flag = Yii::$app->request->post('seller_type', 0);
		} else {
			$filed = Yii::$app->request->get('filter', '');
			$filed_key = trim(Yii::$app->request->get('filter_search_key', ''));
			$val = Yii::$app->request->get('status', '');
			$p_p = Yii::$app->request->get('p_p', '');
			$pre_leader_seller_id = Yii::$app->request->get('p_s_id', '');
			$seller_type_flag = Yii::$app->request->get('seller_type', 0);
		}

		$province_id = $this->getAddressId('province_id');
		$city_id = $this->getAddressId('city_id');
		$area_id = $this->getAddressId('area_id');
		$s = Seller::tableName();
		$m = Member::tableName();


		$query = new Query();
		$query->from(['s' => $s])->leftJoin(['m' => $m], 's.member_id=m.member_id')->leftJoin(['p' => $s], 's.pid=p.seller_id');
		$query->select('s.*,m.phone as login_account,p.seller_name as p_seller_name');

		if ($province_id) {
			$query->andWhere(['s.province_id' => $province_id]);
		}
		if ($city_id) {
			$query->andWhere(['s.city_id' => $city_id]);
		}
		if ($area_id) {
			$query->andWhere(['s.area_id' => $area_id]);
		}

		if ($filed) {
			//$query->andWhere(['s.' . $filed_key => $filed]);
			$query->andWhere('s.' . $filed_key . ' like("%' . $filed . '%")');
		}
		if ($val) {
			$query->andWhere(['s.status' => $val - 1]);
		}
		if ($p_p == '2') {
			//一级商家
			$query->andWhere(['s.pid' => 0]);
		} elseif ($p_p == '1') {
			//子商家
			if ($pre_leader_seller_id) {
				$query->andWhere(['s.pid' => $pre_leader_seller_id]);
			} else {
				$query->andWhere(['<>', 's.pid', 0]);
			}
		}
		if ($seller_type_flag) {
			if ($seller_type_flag == '1') {
				$query->andWhere(['s.is_insurance' => 1]);
			} elseif ($seller_type_flag == '2') {
				$query->andWhere(['s.is_repair' => 1]);
			}
		}
		$count = $query->count('s.seller_id');

		return [$count, $query];
	}

	protected function getListData()
	{
		$pageSize = Yii::$app->request->post('length', 10);
		$start = Yii::$app->request->post('start', 0);//偏移量
		list($count, $query) = $this->getSearchedInfo();
		$queryData = $query->orderBy('s.seller_id DESC')->limit($pageSize)->offset($start)->all();
		$data = [
			'draw' => intval($_REQUEST['draw']),
			'recordsTotal' => $count,
			'recordsFiltered' => $count,
			'data' => []
		];

		foreach ($queryData as $key => $val) {
			$btn = '<a class="btn green btn-xs  btn-default" href="' . $this->createUrl(['seller/view', 'id' => $val['seller_id']]) . '"><i class="fa fa-share"></i> 查看</a>';
			$btn .= '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['seller/perfect', 'member_id' => $val['member_id']]) . '" ><i class="fa fa-pencil">修改</i></a>';
			$btn .= $val['status'] == 1 ?
				'<a class="btn red btn-xs btn-default " onClick="handleStatus(' . $val['seller_id'] . ',' . 0 . ')"  href="javascript:;"><i class="fa fa-trash-o"></i> 终止合作 </a>'
				: '<a class="btn blue btn-xs btn-default" onClick="handleStatus(' . $val['seller_id'] . ',' . 1 . ')" href="javascript:;"><i class="fa fa-check"></i> 重启合作 </a>';
			$btn .= '<button type="button" class="btn btn-xs" onClick="restPwd(' . $val['seller_id'] . ')"><i class="fa fa-eye"></i>重置密码</button>';
			$seller_type = '';
			if ($val['is_insurance'] == 1) {
				$seller_type = '<span class="font-purple-seance">保险</span>';
			}
			if ($val['is_repair'] == 1) {
				if ($seller_type) {
					$seller_type .= '|<span class="font-yellow-casablanca">理赔</span>';
				} else {
					$seller_type = '<span class="font-red">理赔</span>';
				}
			}

			$data['data'][] = array(
				$val['seller_name'],
				$val['login_account'],
				$val['area_info'],
				$val['concat'],
				$val['concat_tel'],
				$val['status'] == 1 ? '<span class="label label-sm label-success">合作中</span>' : '<span class="label label-sm label-danger">已终止</span>',
				$seller_type,
				$val['pid'] == 0 ? '<span class="font-purple-seance">一级商家</span>' : '<span class="font-yellow-casablanca">子商家</span>',
				$val['pid'] == 0 ? '乐换新' : $val['p_seller_name'],
				$btn
			);
		}

		return json_encode($data);
	}


	public function actionChange()
	{
		if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
			return $this->getCheckNo('非法访问!');
		}
		$seller_id = Yii::$app->request->post('seller_id');
		$status = Yii::$app->request->post('status', null);
		if (!$seller_id || is_null($status)) {
			return $this->getCheckNo('参数错误!');
		}
		$seller = Seller::findOne(['seller_id' => $seller_id]);
		if (!$seller) {
			return $this->getCheckNo('查无此商家!');
		}
		if ($status == '1') {
			$seller->status = (int)$status;
			if ($seller->save()) {
				return $this->getCheckYes([], '操作成功!');
			}
			return $this->getCheckNo('重启合作操作失败!');
		}
		//非保险商户
		if (!$seller['is_insurance']) {
			$seller->status = (int)$status;
			if ($seller->save()) {
				return $this->getCheckYes([], '终止合作成功!');
			}
			return $this->getCheckNo('操作失败!');
		}

		try {
			$transaction = Yii::$app->getDb()->beginTransaction();
			//保险商户才冻结卡券
			if ($seller['is_insurance']) {
				//一级商户
				if ($seller['pid'] === 0) {
					//处理 未激活卡券 冻结操作
					CardCouponsGrant::updateAll(['status' => CardCouponsGrant::__STATUS_FROZE], ['seller_id' => $seller_id, 'status' => CardCouponsGrant::__STATUS_DEFAULT]);

				} else {
					$sub_query = (new Query())->select('card_id')->from(CardGrantRelation::tableName())->where(['to_seller_id' => $seller_id]);
					CardCouponsGrant::updateAll(['status' => CardCouponsGrant::__STATUS_FROZE], ['id' => $sub_query, 'status' => CardCouponsGrant::__STATUS_DEFAULT]);
				}
				$seller->status = (int)$status;
				if ($seller->save()) {
					$transaction->commit();
					return $this->getCheckYes([], '终止合作并冻结卡券成功!');
				}
			}

		} catch (Exception $e) {
			$msg = $e->getMessage();

			$transaction->rollback();
		}


		return $this->getCheckNo('操作失败!#' . $msg);
	}

	/**
	 * 完善商户信息
	 * @return string
	 */
	public function actionPerfect()
	{

		$member_id = Yii::$app->request->get('member_id', '');
		$phone = Yii::$app->request->get('phone', '');
		if (!$member_id) {
			if (!$phone) {
				return $this->showMessage('非法访问控制器');
			}
			$user = Member::findOne(['phone' => $phone]);
			$member_id = $user->member_id;
		} else {
			$user = Member::findOne(['member_id' => $member_id]);
		}

		$area = Area::findAll(['area_parent_id' => 0]);
		$seller = Seller::find()->leftJoin('fj_bank as bank', 'bank.member_id = fj_seller.member_id')->where(['fj_seller.member_id' => $member_id])->select('*')->asArray()->one();

		$city_html = '';
		$area_html = '';
		if ($seller) {
			$seller['parent_name'] = $seller['pid'] ? Seller::findOne(['seller_id' => $seller['pid']])->seller_name : '';
			$city_html = helper::getAreaSelect($seller['province_id'], $seller['city_id']);
			$area_html = helper::getAreaSelect($seller['city_id'], $seller['area_id']);
		}
		return $this->render('perfect', ['member_id' => $member_id, 'province' => $area, 'seller' => $this->viewData($seller), 'city_html' => $city_html, 'area_html' => $area_html, 'user' => $user]);
	}

	/**
	 * 返回默认商户信息资料
	 * @param $seller
	 * @return array
	 */
	private function viewData($seller)
	{
		$seller = is_array($seller) ? $seller : [];
		$temp = ['brank_name' => '', 'brank_account' => '', 'account_holder' => '', 'seller_name' => '', 'is_insurance' => '', 'is_repair' => '', 'province_id' => '',
			'city_id' => '', 'area_id' => '', 'detail_address' => '', 'concat' => '', 'concat_tel' => '', 'pid' => '', 'parent_name' => ''];
		return array_merge($temp, $seller);
	}

	/**
	 * 获取地区
	 */
	public function actionGetarea()
	{
		$pid = Yii::$app->request->post('id');
		if (!$pid) {

			return $this->getCheckNo('却少必要的参数');
		}
		$area = Area::find()->where(['area_parent_id' => $pid])->asArray()->all();
		return $this->getCheckYes($area, '');
	}

	/**
	 * Displays a single Seller model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		$seller = $this->findModel($id)->toArray();
		if ($seller) {
			$seller['parent_name'] = $seller['pid'] ? Seller::findOne(['seller_id' => $seller['pid']])->seller_name : '';
		}
		return $this->render('view', [
			'seller' => $seller,
			'bank' => Bank::findOne(['member_id' => $seller['member_id']]),
			'user' => Member::findOne(['member_id' => $seller['member_id']])
		]);
	}

	/**
	 * Creates a new Seller model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax) {
			return $this->showMessage('非法访问控制器');
		}
		$post = Yii::$app->request->post();
		if ($post['account_holder'] && !preg_match('/^[\x4e00-\x9fa5]{2,5}/', $post['account_holder'])) {
			return $this->getCheckNo('开户人必须是中文汉字');
		}
		if ($post['brank_account']) {
			if (strpos($post['brank_name'], '支付宝') !== false) {

			} else {
				if (!preg_match('/\d{15,20}/', $post['brank_account'])) {
					return $this->getCheckNo('银行卡号格式错误');
				}
			}
		}

		if (!$post['member_id'] || !$post['seller_name'] || !$post['concat'] || !$post['concat_tel'] || !$post['province_id'] || !$post['city_id'] || !$post['area_id'] || !$post['detail_address']) {
			return $this->getCheckNo('参数缺失，请注意填写');
		}
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$model = Seller::findOne(['member_id' => $post['member_id']]);
			$model = $model ? $model : new Seller();
			$model->seller_name = $post['seller_name'];
			$model->member_id = intval($post['member_id']);
			$model->concat = $post['concat'];
			$model->concat_tel = $post['concat_tel'];
			$model->is_agreement = intval($post['is_agreement']);
			list($province_id, $procince) = explode(',', $post['province_id']);
			list($city_id, $city) = explode(',', $post['city_id']);
			list($area_id, $area) = explode(',', $post['area_id']);

			$model->province_id = $province_id;
			$model->city_id = $city_id;
			$model->area_id = $area_id;
			$model->area_info = $procince . ' ' . $city . ' ' . $area;
			$model->detail_address = $post['detail_address'];
			if (isset($post['is_type'])) {
//            in_array('1',$post['is_type']) && $model->is_insurance = 1;
//            in_array('2',$post['is_type']) && $model->is_repair=1;

				$model->is_insurance = in_array('1', $post['is_type']) ? 1 : 0;
				$model->is_repair = in_array('2', $post['is_type']) ? 1 : 0;
			}
			if ($post['p_name'] == 2) {
				$model->pid = $post['pid'];
			}
			$model->add_time = time();
			if (!$model->save()) {
				throw  new Exception('商户信息完善失败,请稍后再试.');
			}
			if ($post['brank_name'] && $post['brank_account']) {
				if ($a = $this->checkBrand($post['brank_account'], $post['member_id'])) {
					throw  new Exception('该银行账号已被绑定');
				}
			}
			$model_bank = Bank::findOne(['member_id' => $post['member_id']]);
			$model_bank = $model_bank ? $model_bank : new Bank();
			$model_bank->member_id = intval($post['member_id']);
			$model_bank->account_holder = $post['account_holder'];
			$model_bank->brank_account = $post['brank_account'];
			$model_bank->brank_name = $post['brank_name'];
			$model_bank->add_time = time();
			if (!$model_bank->save()) {
				throw  new Exception('银行账号完善失败');
			}

			$transaction->commit();
			return $this->getCheckYes([], '商户信息完善成功');
		} catch (Exception $e) {
			$transaction->rollBack();
			return $this->getCheckNo($e->getMessage());
		}
	}

	private function checkBrand($account = '', $member_id = 0)
	{
		return Bank::find()->where('member_id != :member_id AND brank_account = :account', [':member_id' => $member_id, ':account' => $account])->one();
	}


	public function actionLevel()
	{
		$search = Yii::$app->request->get('q', '');
		if (!$search) {
			return json_encode([]);
		}
		$seller = Seller::find()->where(['like', 'seller_name', $search])->andWhere(['pid' => 0])->all();
		$data = [];
		foreach ($seller as $val) {
			$data[] = [$val->seller_id, $val->seller_name];
		}
		return json_encode($data);
	}

	public function actionRest()
	{
		if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
			$this->showMessage('非法访问');
		}
		$seller_id = Yii::$app->request->post('seller_id', '');
		if (!$seller_id) {
			return $this->getCheckNo('参数错误');
		}
		$seller = Seller::findOne(['seller_id' => $seller_id]);
		if (!$seller) {
			return $this->getCheckNo('当前商家不存在');
		}
		$model = Member::findOne(['member_id' => $seller->member_id]);
		$pass = $model->createPassword(Member::__DEFAULT_PASS);
		$model->passwd = $pass;
		if ($model->save()) {
			return $this->getCheckYes([], '密码重置成功，默认密码：' . Member::__DEFAULT_PASS);
		} else {
			return $this->getCheckNo('密码重置失败');
		}
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

	/**
	 * @leo.yan 优化
	 * 数据导出
	 */
	public function actionExport()
	{
		list($count, $query) = $this->getSearchedInfo();
		if ($count > 5000) {
			$this->showMessage('导出商家已超过5000', '警告提示', 'danger', 'javascript:window.close();');
		}
		$queryData = $query->orderBy('s.seller_id DESC')->limit(5000)->all();
		$respon = [];
		if ($queryData) {
			foreach ($queryData as $val) {
				$seller_type = '';
				if ($val['is_insurance'] == 1) {
					$seller_type = '保险';
				}
				if ($val['is_repair'] == 1) {
					if ($seller_type) {
						$seller_type .= '|理赔';
					} else {
						$seller_type = '理赔';
					}
				}
				$respon[] = [
					$val['seller_name'],
					$val['login_account'],
					$val['area_info'],
					$val['concat'],
					$val['concat_tel'],
					$val['status'] == 1 ? '合作中' : '已终止',
					$seller_type,
					$val['pid'] == 0 ? '一级商家' : '子商家',
					$val['pid'] == 0 ? '乐换新' : $val['p_seller_name']
				];

			}
		}
		set_time_limit(0);
		theCsv::export([
			'data' => $respon,
			'name' => "seller_list_" . date('Y_m_d_H', time()) . ".csv",    // 自定义导出文件名称
			'header' => ['商家名称', '登录账号', '所在区域', '联系人', '联系电话', '合作状态', '商家类型', '商家等级', '所属上级']
		]);
	}

}
