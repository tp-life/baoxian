<?php
namespace backend\controllers;
use common\models\RoleAccess;
use common\models\RoleModule;
use Yii;
use common\models\Role;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RoleController implements the CRUD actions for Role model.
 */
class RoleController extends LoginedController
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
			if ($id = intval(Yii::$app->request->post('id', 0))) {
				$conditon['id'] = $id;
			}
			if ($name = trim(Yii::$app->request->post('name', ''))) {
				$conditon['name'] = $name;
			}
			$isSytem = trim(Yii::$app->request->post('isSystem', ''));
			if ($isSytem !== '') {
				$conditon['is_system'] = $isSytem;
			}

			$total = Role::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => Role::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);
			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					//$btn = '<a class="btn green btn-xs  btn-default" href="' . $this->createUrl(['role/view', 'id' => $item->id]) . '"><i class="fa fa-share"></i> 查看</a>';
					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['role/update', 'id' => $item->id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id . '"  rel="' . $this->createUrl(['role/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					$respon[] = [
						$item->id,
						$item->name,
						$item->is_system ? '是' : '否',
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
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new Role model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
	public function actionCreate()
	{
		$model = new Role();
		$this->_dataAdapter($model);
		$access_list = RoleModule::findAll(['is_effect'=>1]);
		return $this->render('create', ['model' => $model,'access_list'=>$access_list,'role_access'=>array()]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('name', Yii::$app->request->post('name', ''));
			$model->setAttribute('is_system', Yii::$app->request->post('is_system', 0));
			$role_access = Yii::$app->request->post('role_access', null);
			if (empty($role_access)) {
				$this->showMessage('请选择角色权限', '错误提示', self::__MSG_DANGER, Url::to(['role/index']));
			}
			$flag = $model->isNewRecord;
			$_message = $flag ? '新增角色成功' : "角色修改成功";
			if ($model->save()) {
				if (!$flag) {
					//update
					RoleAccess::deleteAll('role_id=:role_id', [':role_id' => $model->id]);
				}
				foreach ($role_access as $module_action) {
					list($module, $action) = explode('_', $module_action);
					$object = new RoleAccess();
					$object->role_id = $model->id;
					$object->action_id = $action;
					$object->module_id = $module;
					$object->save(false);
				}

				Yii::$app->cache->delete('nav_');//刷新缓存 nav
				Yii::$app->cache->delete('data_'.$model->id);//刷新对应缓存 access data
				Yii::$app->cache->delete('role_'.$model->id);//刷新对应缓存 role access data

				$this->showMessage($_message, '提示信息', self::__MSG_INFO, Url::to(['role/index']));
			}
			$this->showMessage($model->getErrors(), '错误提示', self::__MSG_DANGER, Url::to(['role/index']));
		}
	}

	/**
	 * Updates an existing Role model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$this->_dataAdapter($model);
		$access_list = RoleModule::findAll(['is_effect'=>1]);
		$role_list = RoleAccess::findAll(['role_id'=>$model->id]);
		$role_access = array();
		if($role_list){
			foreach($role_list as $role){
				$role_access[$role['module_id']][$role['action_id']] = $role;
			}
		}
		return $this->render('update', ['model' => $model,'access_list'=>$access_list,'role_access'=>$role_access]);

	}

    /**
     * Deletes an existing Role model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if($this->findModel(Yii::$app->request->get('id',0))->delete()){
			return $this->getCheckYes(null,'删除成功');
		}
		return $this->getCheckNo('删除失败 ');
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
        if (($model = Role::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
