<?php
namespace backend\controllers;
use common\models\RoleNav;
use common\models\RoleNavGroup;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class NavController extends LoginedController
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
			$group_id = Yii::$app->request->post('group_id',0);
			$conditon = ['nav_id'=>$group_id];
			$params = array();
			$total = RoleNav::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => RoleNav::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['nav/update', 'id' => $item->id,'group_id'=>$group_id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id . '"  rel="' . $this->createUrl(['nav/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					$respon[] = [
						$item->name,
						$item->icon,
						$item->is_effect?'是':'否',
						$item->sort,
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}
		$navGroup = $this->loadNavGroupModel();
		return $this->render('index',['navGroup'=>$navGroup]);
	}

	public function actionCreate()
	{
		$model = new RoleNav();
		$this->_dataAdapter($model);
		$navGroup = $this->loadNavGroupModel();
		$model->nav_id = $navGroup->id;
		$model->is_effect = 1;
		$model->sort = 99;
		return $this->render('create', ['model' => $model,'navGroup'=>$navGroup]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('name', strtolower(Yii::$app->request->post('name', '')));
			$model->setAttribute('icon', Yii::$app->request->post('icon', ''));
			$model->setAttribute('is_effect', Yii::$app->request->post('is_effect', 1));
			$model->setAttribute('sort', Yii::$app->request->post('sort', 100));
			$model->setAttribute('nav_id', Yii::$app->request->post('nav_id', 0));
			$_message = $model->isNewRecord ? '新增菜单成功' : "菜单修改成功";
			if ($model->save()) {
				Yii::$app->cache->flush();//刷新权限缓存处理
				$this->showMessage($_message,'',self::__MSG_INFO,Url::to(['nav/index','group_id'=>Yii::$app->request->post('nav_id', 0)]));
			}
			$this->showMessage($model->getErrors(),'',self::__MSG_DANGER,Url::to(['nav/index','group_id'=>Yii::$app->request->post('nav_id', 0)]));
		}
	}

	public function actionUpdate($id)
	{

		$model = $this->findModel($id);
		$this->_dataAdapter($model);
		$navGroup = $this->loadNavGroupModel();
		return $this->render('update', ['model' => $model,'navGroup'=>$navGroup]);

	}


	public function actionDelete()
	{
		if($this->findModel(Yii::$app->request->get('id',0))->delete()){
			return $this->getCheckYes(null,'删除成功');
		}
		return $this->getCheckNo('删除失败 ');
	}

	protected function loadNavGroupModel()
	{
		$module = RoleNavGroup::findOne(['id'=>Yii::$app->request->get('group_id',0)]);
		if(empty($module)){
			$this->showMessage('查无父级模块记录','错误提示',self::__MSG_DANGER,$this->createUrl(['group/index']));
		}
		return $module;
	}

    protected function findModel($id)
    {
        if (($model = RoleNav::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
