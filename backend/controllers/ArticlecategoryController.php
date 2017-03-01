<?php
namespace backend\controllers;
use common\models\Article;
use common\models\ArticleCategory;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\LoginedController;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ArticlecategoryController extends LoginedController
{

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

			if ($title = trim(Yii::$app->request->post('title', ''))) {
				$conditon['title'] = $title;
			}
			$pid = trim(Yii::$app->request->post('pid', ''));
			if ($pid !== '') {
				$conditon['pid'] = intval($pid);
			}
			$is_effect = trim(Yii::$app->request->post('is_effect', ''));
			if ($is_effect !== '') {
				$conditon['is_effect'] = intval($is_effect);
			}

			$total = ArticleCategory::find()->where($conditon, $params)->count('id');
			$dataProvider = new ActiveDataProvider([
				'query' => ArticleCategory::find()->where($conditon, $params)->orderBy('sort ASC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);
			if ($data = $dataProvider->models) {
				foreach ($data as $item) {

					$btn = '<a class="btn btn-xs default btn-editable" href="' . $this->createUrl(['articlecategory/update', 'id' => $item->id]) . '" ><i class="fa fa-pencil">修改</i></a>';
					if($item->is_effect)
					{
						$btn .= '<a class="btn red btn-xs btn-default bootbox-confirm" data-id="' . $item->id . '"  rel="' . $this->createUrl(['articlecategory/delete', 'id' => $item->id]) . '" href="javascript:;"><i class="fa fa-trash-o"></i> 删除 </a>';
					}
					$respon[] = [
						$item->id,
						$item->title,
						$item->brief,
//						ArticleCategory::$articleType[$item->pid],
						$item->is_effect ? '是' : '否',
						$item->sort,
						$btn
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index',['categoryType'=>ArticleCategory::$articleType]);
	}

	public function actionCreate()
	{
		$model = new ArticleCategory();
		$this->_dataAdapter($model);
        //得到分类
        $ArticleCategoryList = $model->treeArticleCategoryList(2);
        $arr[0] = '顶级分类';
        foreach ($ArticleCategoryList as $item) {
            $arr[$item['id']] = str_repeat('　', ($item['deep']-1)*2).$item['title'];
        }
        Yii::$app->view->params['open_appasset'] = true;
		return $this->render('create', ['model' => $model,'ArticleCategoryList'=>$arr]);
	}

	protected function _dataAdapter($model)
	{
        $request = Yii::$app->request;
		if ($request->isPost) {
            $data = $request->post();
            $msg = '编辑';
            $where['pid'] = $data['ArticleCategory']['pid'];
            $where['title'] = $data['ArticleCategory']['title'];
            $query = ArticleCategory::find()->where($where);
            if ($data['ArticleCategory']['id']) {
                $query = $query->andWhere(['<>', 'id',$data['ArticleCategory']['id']]);
                $msg = '添加';
            }
            if ($query->one()) {
                $this->showMessage('该分类下已存在同名分类','',self::__MSG_DANGER);
            }

            $data['ArticleCategory']['is_effect'] = 1;
            $data['ArticleCategory']['sort'] = $data['ArticleCategory']['sort'] ? $data['ArticleCategory']['sort'] : 255;
			if ($model->load($data) && $model->validate() && $model->save()) {
				$this->showMessage($msg.'成功','',self::__MSG_INFO,Url::to(['articlecategory/index']));
			}else{
				$this->showMessage($this->getModelErrorsStr($model->getErrors()),'',self::__MSG_DANGER);
			}
		}
	}

	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		$this->_dataAdapter($model);
        $ArticleCategoryList = $model->treeArticleCategoryList();
        $arr[0] = '顶级分类';
        foreach ($ArticleCategoryList as $item) {
            $arr[$item['id']] = str_repeat('　', ($item['deep']-1)*2).$item['title'];
        }
        Yii::$app->view->params['open_appasset'] = true;
		return $this->render('update', ['model' => $model,'ArticleCategoryList'=>$arr]);

	}

	public function actionDelete()
	{
        //得到该分类下的所有分类
        $id = Yii::$app->request->get('id',0);
        $ids = ArticleCategory::getChildrenIds($id);
        $ids = explode(',', $ids);
		if(ArticleCategory::deleteAll(['id'=>$ids]) && Article::deleteAll(['category_id'=>$ids])){
			return $this->getCheckYes(null,'删除成功');
		}
		return $this->getCheckNo('删除失败 ');
	}


	protected function findModel($id)
	{
		if (($model = ArticleCategory::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
