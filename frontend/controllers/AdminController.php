<?php

namespace frontend\controllers;


use m35\thecsv\theCsv;
use Yii;
use common\models\Admin;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\JsExpression;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends Controller
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
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Admin::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
	public function actionCkform()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;;
		$model = new Admin();
		$model->load(Yii::$app->request->post());
		return  \yii\widgets\ActiveForm::validate($model);
	}

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
	public function actionExport()
	{
		echo phpinfo();
		//通过条件组合数据
		//500 2000 4

		/* for($i=100;$i<105;$i++){
			 //echo "<pre>";
			 echo  theCsv::export([
					 'data'=>[['aaa','bbb'],['aaa','bbb']],
					 'name' => "data_{$i}.csv",    // 自定义导出文件名称
					 ///'target'=> Yii::getAlias('@runtime').'/'
				 ]
			 );
			 //echo "<br/>";
		 }*/
/*		for($i=100;$i<105;$i++){
			$js=<<<JS
<script type='text/javascript'>window.open('/admin/exportdata?_t={$i}');</script>
JS;
			echo $js;usleep(1000);

		}*/

	}
	public function actionExportdata()
	{
		$_t = Yii::$app->request->get('_t',0);
		$pathfile= Yii::getAlias('@runtime').'/data_'.$_t.'.csv';
		//var_dump(is_readable($pathfile));die;
		if(is_readable($pathfile) ){
			Yii::$app->response->sendFile($pathfile);
			/*return theCsv::export([
					'fp' => $fp,    // 自定义导出文件名称
				]
			);*/
		}else{
			die('ssss');
		}




	}
}