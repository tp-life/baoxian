<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\Article;
use common\models\ArticleCategory;
use common\models\InsuranceType;
use common\models\Role;
use Yii;
use common\models\Admin;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ArticleController extends LoginedController
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [];

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
			$query->from(Article::tableName());
			$data = Yii::$app->request->post();
			$get = Yii::$app->request->get();
			if ($data['id'] !== '') {
				$query->andWhere('id=:id', [':id' => intval($data['id'])]);
			}
			if ($title = trim($data['title'])) {
				$query->andWhere('title LIKE :title', [':title' => "%$title%"]);
			}
			if ($data['pid'] || $data['pid'] ==='0') {
				$query->andWhere('category_id=:pid', [':pid' => $data['pid']]);
			}
			if ($get['cid']) {
				if(!$data['pid'] && $data['pid'] !=='0')
				$query->andWhere('coverage_type_id=:cid', [':cid' => $get['cid']]);
			}
			if ($data['author']) {
				$query->andWhere('author LIKE :author', [':author' => "%{$data['author']}%"]);
			}
			if ($data['status'] !== '') {
				$query->andWhere('status =:status', [':status' => $data['status'] ? 1 : 0]);
			}
			if ($data['login_at_from']) {
				$query->andFilterCompare('add_time', strtotime($data['login_at_from']), '>=');
			}
			if ($data['login_at_to']) {
				$login_at_to = $data['login_at_to'] . " 23:59:59";
				$query->andFilterCompare('add_time', strtotime($login_at_to), '<=');
			}


			$total = $query->count('id');
			$data = $query->orderBy('sort ASC')->limit($pageSize)->offset($start)->all();

			if ($data) {
				$article_model = new Article();
				foreach ($data as $item) {
					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['article\update', 'id' => $item['id']]) . '" ><i class="fa fa-pencil">修改</i></a>';
					if ($item['is_system'] == 0) {
						$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item['id'] . '"  rel="' . $this->createUrl(['article\delete', 'id' => $item['id']]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					}
					$cate=$article_model->getArticleCategory($item['category_id']);
					$respon[] = [
						$item['id'],
						$item['title'],
						$cate?$cate:' 保险系列(<span class="font-purple-medium"> '.InsuranceType::findOne(['id'=>$item['coverage_type_id']])->type_name.'</span> )',
						$item['author'],
						$item['status'] ? '启用' : '禁用',
						$item['sort'],
						date('Y-m-d H:i', $item['add_time']),
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}
		$ArticleCategoryList = (new ArticleCategory())->treeArticleCategoryList(2);
		$pid=Yii::$app->request->get('pid',null);
		return $this->render('index', ['ArticleCategoryList' => $ArticleCategoryList,'url'=>$this->createUrl(['article/index','cid'=>$pid])]);
	}


	/**
	 * Creates a new Admin model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Article();
		if (Yii::$app->request->isPost) {
			$this->_dataAdapter($model);
		} else {
			//得到分类
			$ArticleCategoryList = (new ArticleCategory())->treeArticleCategoryList(2);

			$arr = $coverage= [];
			$arr[0] = '保险系列';
			foreach ($ArticleCategoryList as $item) {
				$arr[$item['id']] = str_repeat('　', ($item['deep'] - 1) * 2) . $item['title'];
			}
			$coverage_model = InsuranceType::findAll(['status'=>1]);
			foreach($coverage_model as $val){
				$coverage[$val -> id ] =$val->type_name;
			}
			$model->tag_id =1;
			return $this->render('create', [
				'model' => $model,
				'ArticleCategoryList' => $arr,
				'coverage'=>$coverage
			]);
		}
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
		if (Yii::$app->request->isPost) {
			$this->_dataAdapter($model);
		} else {
			$ArticleCategoryList = (new ArticleCategory())->treeArticleCategoryList(2);
			$arr = [];

			foreach ($ArticleCategoryList as $item) {
				$arr[$item['id']] = str_repeat('　', ($item['deep'] - 1) * 2) . $item['title'];
			}
			$arr[0] = '保险系列';
			$coverage_model = InsuranceType::findAll(['status'=>1]);
			foreach($coverage_model as $val){
				$coverage[$val -> id ] =$val->type_name;
			}
			return $this->render('update', [
				'model' => $model,
				'ArticleCategoryList' => $arr,
				'coverage'=>$coverage
			]);
		}
	}

	private function _dataAdapter($model)
	{
		$request = Yii::$app->request;
		if ($request->isPost) {
			$data = $request->post();
			$data['Article']['sort'] = $data['Article']['sort'] ? $data['Article']['sort'] : 255;
			$data['Article']['status'] = $data['Article']['status'] ? 1 : 0;
			$msg = '编辑';
			if (!$data['Article']['id']) {
				$data['Article']['author'] = Yii::$app->user->identity->attributes['username'];
				$msg = '添加';
			}
			if ($model->load($data) && $model->validate() && $model->save()) {
				$this->showMessage($msg . '成功', '', self::__MSG_INFO, Url::to(['article/index']));
			} else {
				$this->showMessage($this->getModelErrorsStr($model->getErrors()), '', self::__MSG_DANGER);
			}
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
		if (($model = Article::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
