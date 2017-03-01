<?php
namespace maintainer\controllers;

use common\models\Article;
use common\models\Msg;
use maintainer\components\BaseController;
use Yii;
use maintainer\models\LoginForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

/**
 * Site controller
 */
class SiteController extends BaseController
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/**
	 * Displays homepage.
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['site/login']);
		}
		$seller_id=Yii::$app->user->identity->getSellerInfo()->seller_id;
		if($seller_id == 1){
			return $this->redirect(['site/login']);
		}
		$model = Msg::find();
		$model->where(['seller_id'=>$seller_id]);
		$count=$model->count();
		$pageSize=15;
		$page=Yii::$app->request->get('page',0);
		if($page < 1 ){
			$page =0;
		}
		$dataProvider = new ActiveDataProvider([
				'query' =>$model->orderBy('status asc')->limit($pageSize)->offset($pageSize*$page)->asArray(),
				'pagination' => [
						'pageSize' => $pageSize,
						'page' => $page,
						'totalCount' => $count
				]
		]);
		$result=$dataProvider->getModels();

		return $this->render('index',['result'=>$result,'page'=>$page,'total_page'=>ceil($count/$pageSize)]);
	}

	/**
	 * Login action.
	 *
	 * @return string
	 */
	public function actionLogin()
	{
		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->loginSeller()) {
			return $this->redirect(['site/index']);
		} else {
			$this->layout = 'login_layout';//未登录 布局
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}


	public function actionHandlemsg(){
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['site/login']);
		}

		if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
			$id = Yii::$app->request->post('id','');
			if(!$id){
				return $this->getCheckNo('参数错误!');
			}
			$member_id = Yii::$app->user->identity->id;
			$model = Msg::findOne(['id'=>$id]);
			$model->status = 1 ;
			$model->read_id = $member_id;
			$model->read_time =time();
			if($model->save()){
				return $this->getCheckYes([],'操作成功!');
			}
			return $this->getCheckNo('操作失败!');
		}
	}

	/**
	 * Logout action.
	 *
	 * @return string
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout(true);
		return $this->goHome();
	}

}
