<?php
namespace backend\controllers;
use common\models\RoleNav;
use common\models\RoleNavGroup;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\db\Transaction;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class GroupController extends LoginedController
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

			$total = RoleNavGroup::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => RoleNavGroup::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$btn = '<a class="btn green btn-xs  btn-default" href="' . $this->createUrl(['nav/index', 'group_id' => $item->id]) . '"><i class="fa fa-share"></i>子菜单</a>';
					$btn .= '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['group/update', 'id' => $item->id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id . '#栏目下所有子菜单都被删除"  rel="' . $this->createUrl(['group/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					$respon[] = [
						$item->name,
						$item->icons,
						$item->is_effect?'是':'否',
						$item->sort,
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
		$model = new RoleNavGroup();
		$this->_dataAdapter($model);
		$model->is_effect=1;//default
		$model->sort=99;//default
		return $this->render('create', ['model' => $model]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('name', strtolower(Yii::$app->request->post('name', '')));
			$model->setAttribute('icons', Yii::$app->request->post('icons', ''));
			$model->setAttribute('is_effect', Yii::$app->request->post('is_effect', 1));
			$model->setAttribute('sort', Yii::$app->request->post('sort', 100));
			$_message = $model->isNewRecord ? '新增菜单成功' : "菜单修改成功";
			if ($model->save()) {
				$this->showMessage($_message,'',self::__MSG_INFO,Url::to(['group/index']));
			}
			$this->showMessage($model->getErrors(),'',self::__MSG_DANGER,Url::to(['group/index']));
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
				RoleNav::deleteAll(['nav_id' => $model->id]);
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
        if (($model = RoleNavGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
