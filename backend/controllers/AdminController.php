<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\AdminLog;
use common\models\Role;
use Yii;
use common\models\Admin;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends LoginedController
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [];
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
	 * Lists all Admin models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		if (Yii::$app->request->isAjax) {
			$respon = array();
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量

			$query = new Query();
			$query->select('a.*,r.name')->from(['a' => Admin::tableName(), 'r' => Role::tableName()])->where('a.role_id=r.id');
			if ($id = intval(Yii::$app->request->post('id', 0))) {
				$query->andWhere('a.id=:id', [':id' => $id]);
			}
			if ($username = trim(Yii::$app->request->post('username', ''))) {
				$query->andWhere('a.username =:username', [':username' => $username]);
			}
			if ($phone = trim(Yii::$app->request->post('phone', ''))) {
				$query->andWhere('a.phone=:phone', [':phone' => $phone]);
			}
			$isSytem = trim(Yii::$app->request->post('is_system', ''));
			if ($isSytem !== '') {
				$query->andWhere('a.is_system=:is_system', [':is_system' => $isSytem]);
			}
			if ($role_name = trim(Yii::$app->request->post('role_name', ''))) {
				$query->andWhere('b.name =:role_name', [':role_name' => $role_name]);
			}
			if ($login_at_from = Yii::$app->request->post('login_at_from', '')) {
				$query->andFilterCompare('a.login_at', strtotime($login_at_from), '>=');
			}
			if ($login_at_to = Yii::$app->request->post('login_at_from', '')) {
				$login_at_to = $login_at_to . " 23:59:59";
				$query->andFilterCompare('a.login_at', strtotime($login_at_to), '<=');
			}


			$total = $query->count('a.id');
			$data = $query->orderBy('a.id DESC')->limit($pageSize)->offset($start)->all();

			if ($data) {
				foreach ($data as $item) {

					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['admin/update', 'id' => $item['id']]) . '" ><i class="fa fa-pencil">修改</i></a>';
					if ($item['is_system'] == 0) {
						$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item['id'] . '"  rel="' . $this->createUrl(['admin/delete', 'id' => $item['id']]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					}
					$btn .= '<a class="btn btn-xs default btn-editable" target="_blank" href="' . $this->createUrl(['admin/log', 'id' => $item['id']]) . '" >日志</i></a>';
					$respon[] = [
						$item['id'],
						$item['username'],
						$item['phone'],
						$item['is_system'] ? '是' : '否',
						$item['name'],
						$item['login_at'] ? '<span class="font-green-jungle">'.date('Y-m-d H:i', $item['login_at']).'</span>' : '离线',
						$item['login_ip'],
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index');
	}

	/**
	 * 验证admin 唯一
	**/
	public function actionCkadmin()
	{
		$admin = trim($_REQUEST['username']);
		if (empty($admin)) {
			echo 'false';exit;
		}
		echo Admin::findOne(['username' => $admin]) ? 'false' : 'true';
	}


	public function actionCreate()
	{
		$model = new Admin();
		$this->_dataAdapter($model);
		$model->status=1;//default
		$role = Role::getAll();
		$roleArray = array_column($role,'name','id');
		return $this->render('create', ['model' => $model,'roleArray'=>$roleArray]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('username', Yii::$app->request->post('username', ''));
			$model->isNewRecord && $model->setPassword( Yii::$app->request->post('password', ''));
			$model->setAttribute('phone', Yii::$app->request->post('phone', ''));
			$model->setAttribute('is_system', Yii::$app->request->post('is_system', 0));
			$model->setAttribute('status', Yii::$app->request->post('status', 1));
			$model->setAttribute('role_id', Yii::$app->request->post('role_id', 0));
			$_message = $model->isNewRecord ? '新增管理员成功' : "编辑成功";
			if ($model->save()) {
				$this->showMessage($_message,'',self::__MSG_INFO,Url::to(['admin/index']));
			}
			$this->showMessage($model->getErrors(),'',self::__MSG_DANGER,Url::to(['admin/index']));
		}
	}

	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$this->_dataAdapter($model);
		$role = Role::getAll();
		$roleArray = array_column($role,'name','id');
		return $this->render('update', ['model' => $model,'roleArray'=>$roleArray]);

	}

	/**
	 * Deletes an existing Admin model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		if($this->findModel(Yii::$app->request->get('id',0))->delete()){
			return $this->getCheckYes(null,'删除成功');
		}
		return $this->getCheckNo('删除失败 ');
	}

	/**
	 * Finds the Admin model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Admin the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Admin::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Lists all Admin log models.
	 * @return mixed
	 */
	public function actionLog()
	{
		if (Yii::$app->request->isAjax) {
			$respon = array();
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量
			$username = Yii::$app->request->post('username', '');
			$uid = intval($_REQUEST['id']);
			$conditon = [];
			if ($username) {
				$conditon['username'] = $username;
			}
			if($uid){
				$conditon['uid'] = $uid;
			}
			$params = array();
			$total = AdminLog::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => AdminLog::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$respon[] = [
						$item->id,
						$item->username,
						'<span class="font-green">' . $item->description . '</span>',
						$item->created_at,
						''
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('admin_log');
	}

}
