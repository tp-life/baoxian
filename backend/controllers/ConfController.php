<?php
namespace backend\controllers;
use common\models\Conf;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ConfController extends LoginedController
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
			if ($isSytem = intval(Yii::$app->request->post('isSystem', 99))) {
				if ($isSytem != 99) {
					$conditon['is_system'] = $isSytem;
				}
			}
			$total = Conf::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => Conf::find()->where($conditon, $params)->orderBy('id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);
			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['conf/update', 'id' => $item->id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id .'#Key='.$item->name.'"  rel="' . $this->createUrl(['conf/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					$respon[] = [
						$item->name,
						$item->value,
						$item->groupTypeTxt,
						$item->china_name,
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index',['confType'=>Conf::$confType]);
	}



	public function actionCreate()
	{
		$model = new Conf();
		$this->_dataAdapter($model);
		$model->group_id=Conf::CONF_PROJECT;//default

		return $this->render('create', ['model' => $model]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$model->setAttribute('name', Yii::$app->request->post('name', ''));
			$model->setAttribute('value', Yii::$app->request->post('value', ''));
			$model->setAttribute('group_id', Yii::$app->request->post('group_id', ''));
			$model->setAttribute('china_name', Yii::$app->request->post('china_name', ''));
			$_message = $model->isNewRecord ? '新增配置成功' : "配置编辑成功";
			if ($model->save()) {
				//flash cache for key - value
				Yii::$app->cache->delete($model->name);

				$this->showMessage($_message,'',self::__MSG_INFO,Url::to(['conf/index']));
			}
			$this->showMessage($model->getErrors(),'',self::__MSG_DANGER,Url::to(['conf/index']));
		}
	}

	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$this->_dataAdapter($model);
		return $this->render('update', ['model' => $model]);

	}


    /**
     * Deletes an existing Role model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
		$model = $this->findModel(Yii::$app->request->get('id',0));
		if($name = $model->name){
			if($model->delete()){
				//flash cache for key - value
				Yii::$app->cache->delete($name);
				return $this->getCheckYes(null,'删除成功');
			}
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
        if (($model = Conf::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
