<?php
namespace backend\controllers;
use common\models\RoleAction;
use common\models\RoleModule;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ModuleController extends LoginedController
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
			$conditon = array();
			$params = array();
			$total = RoleModule::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => RoleModule::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$btn = '<a class="btn green btn-xs  btn-default" href="' . $this->createUrl(['modulefun/index', 'module_id' => $item->id]) . '"><i class="fa fa-share"></i>模块方法</a>';
					$btn .= '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['module/update', 'id' => $item->id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id . '#模块下所有方法都被删除"  rel="' . $this->createUrl(['module/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					$respon[] = [
						$item->id,
						$item->module,
						$item->name,
						$item->is_effect?'是':'否',
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index');
	}

	public function actionCreate()
	{
		$model = new RoleModule(['scenario'=>RoleModule::ADMIN_CREATE]);
		$this->_dataAdapter($model);
		$model->is_effect=1;//default
		return $this->render('create', ['model' => $model]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('module', strtolower(Yii::$app->request->post('module', '')));
			$model->setAttribute('name', Yii::$app->request->post('name', ''));
			$model->setAttribute('is_effect', Yii::$app->request->post('is_effect', 1));
			$_message = $model->isNewRecord ? '新增模块成功' : "模块修改成功";
			if ($model->save()) {
				$this->showMessage($_message,'',self::__MSG_INFO,Url::to(['module/index']));
			}
			$this->showMessage($model->getErrors(),'',self::__MSG_DANGER,Url::to(['module/index']));
		}
	}

	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$this->_dataAdapter($model);
		return $this->render('update', ['model' => $model]);

	}

	public function actionDelete()
	{
		$model = $this->findModel(Yii::$app->request->get('id', 0));
		if ($model) {
			//事务处理
			$transaction = Yii::$app->getDb()->beginTransaction(Transaction::READ_COMMITTED);
			try {
				RoleAction::deleteAll(['module_id' => $model->id]);
				$model->delete();
				$transaction->commit();
				return $this->getCheckYes(null, '删除成功');
			} catch (Exception $e) {
				$transaction->rollBack();
				return $this->getCheckNo('操作失败#' . $e->getMessage());
			}
		}
		return $this->getCheckNo('查无记录，无效模块');
	}


    protected function findModel($id)
    {
        if (($model = RoleModule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }




}
