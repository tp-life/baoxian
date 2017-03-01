<?php
namespace backend\controllers;
use common\models\RoleAction;
use common\models\RoleModule;
use common\models\RoleNav;
use common\models\RoleNavGroup;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ModulefunController extends LoginedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
		return [];
    }

	public function actionIndex()
	{
		if (Yii::$app->request->isAjax) {
			$respon = array();
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量
			$module_id = Yii::$app->request->post('module_id',0);
			$conditon = array();
			$params = array();
			$conditon['module_id'] = intval($module_id);

			$total = RoleAction::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => RoleAction::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['modulefun/update', 'id' => $item->id,'module_id'=>$module_id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id . '"  rel="' . $this->createUrl(['modulefun/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					$respon[] = [
						$item->id,
						$item->name,
						$item->action,
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		$module = $this->loadModuleModel();
		return $this->render('index',['module'=>$module]);
	}

	public function actionCreate()
	{
		$model = new RoleAction();
		$model->scenario = RoleAction::ADMIN_CREATE;
		$this->_dataAdapter($model);
		$module = $this->loadModuleModel();
		$navGroup = RoleNav::getAll();
		$navArray = array_column($navGroup,'name','id');
		$model->module_id = $module->id;
		return $this->render('create', ['model' => $model,'module'=>$module,'navArray'=>$navArray]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('action', strtolower(Yii::$app->request->post('action', '')));
			$model->setAttribute('name', Yii::$app->request->post('name', ''));
			$model->setAttribute('group_id', Yii::$app->request->post('group_id', 0));
			$model->setAttribute('module_id', Yii::$app->request->post('module_id', 0));
			$_message = $model->isNewRecord ? '模块方法添加成功' : "模块方法修改成功";
			if ($model->save()) {
				$this->showMessage($_message,'',self::__MSG_INFO,Url::to(['modulefun/index','module_id'=>Yii::$app->request->post('module_id', 0)]));
			}
			$this->showMessage($model->getErrors(),'',self::__MSG_DANGER,Url::to(['modulefun/index','module_id'=>Yii::$app->request->post('module_id', 0)]));
		}
	}

	public function actionUpdate($id)
	{

		$model = $this->findModel($id);
		$this->_dataAdapter($model);
		$module = $this->loadModuleModel();
		$navGroup = RoleNav::getAll();
		$navArray = array_column($navGroup,'name','id');
		return $this->render('update', ['model' => $model,'module'=>$module,'navArray'=>$navArray]);

	}


    public function actionDelete()
    {
        if($this->findModel(Yii::$app->request->get('id',0))->delete()){
			return $this->getCheckYes(null,'删除成功');
		}
		return $this->getCheckNo('删除失败 ');
    }


    protected function findModel($id)
    {
        if (($model = RoleAction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	protected function loadModuleModel()
	{
		$module = RoleModule::findOne(['id'=>Yii::$app->request->get('module_id',0)]);
		if(empty($module)){
			$this->showMessage('查无父级模块记录','错误提示',self::__MSG_DANGER,$this->createUrl(['module/index']));
		}
		return $module;
	}
}
