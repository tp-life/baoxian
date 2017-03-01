<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\BrandModel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class BrandController extends LoginedController
{
    public function actionIndex()
    {
        $id = Yii::$app->request->get('id', null);
        $url = $this->createUrl(['brand/getdata', 'parent_id' => $id]);
        return $this->render('index', ['url' => $url]);
    }


    public function actionGetdata()
    {
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $parent_id = Yii::$app->request->get('parent_id', 0);
        $model = $this->_condition(['parent_id' => $parent_id]);
        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->orderBy('sort asc,id asc')->limit($pageSize)->offset($start),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);

        $brand = $dataProvider->getModels();
        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        $model = new  BrandModel();
        foreach ($brand as $key => $val) {
            $btn = '<a class="btn green btn-xs btn-default " href="' . $this->createUrl(['brand/create', 'id' => $val->id]) . '"><i class="fa fa-plus-square"></i> 新增下级 </a>';
            $btn .= '<a class="btn btn-xs default"  href="' . $this->createUrl(['brand/index', 'id' => $val->id]) . '"><i class="fa fa-eye"></i> 查看下级 </a>';
            $btn .= '<a class="btn red btn-xs btn-default"  href="javascript:void(0)" onclick="deleteBrand('.$val->id.')"><i class="fa fa-trash-o"></i> 删除 </a>';
            $btn .= '<a class="btn  btn-xs btn-default"  href="'.$this->createUrl(['brand/edit', 'id' => $val->id]).'"><i class="fa fa-edit"></i> 修改 </a>';
            $btn = $val->depth >= 3 ? '' : $btn;
            $data['data'][] = array(
//                '<input type="checkbox" name="id[]" value="'.$val->id.'">',
                $val->id,
                $val->model_name,
                $val->parent_id ? $model->getBrand($val->parent_id)->model_name : '顶层分类',
                $val->first_word,
                $val->sort,
                $btn
            );
        }

        return json_encode($data);
    }

    private function _condition($condition = array())
    {
        $model = BrandModel::find();
        $get=Yii::$app->request->get();
        $post=Yii::$app->request->post();
        $tj=array_merge($get,$post);
        $where=[];
        if(isset($tj['name']) && !empty($tj['name'])){
            $model->where(['like','model_name',$tj['name']]);
        }
        $condition=array_merge($where,$condition);
        $model->andWhere($condition);
        return $model;
    }


    public function actionCreate()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id', 0);
            $name = Yii::$app->request->post('model_name', '');
            $depth = Yii::$app->request->post('depth', 1);
            $sort = trim(Yii::$app->request->post('sort', 255));
            if (!$name) {
                return $this->getCheckNo('却少必须参数!');
            }
            $brand = explode('#', $name);
            $value = [];
            $sql = 'INSERT INTO ' . BrandModel::tableName() . ' (`parent_id`,`model_name`,`sort`,`depth`)  VALUES ';
            foreach ($brand as $k => $v) {
                $key = ':model_key' . $k;
                $sql .= " ( :parent_id , {$key} ,:sort, :depth),";
                $value[':parent_id'] = $id;
                $value[$key] = $v;
                $value[':sort']=intval($sort);
                $value[':depth'] = $depth;
            }
            $sql = trim($sql, ',');
            $ret = Yii::$app->getDb()->createCommand($sql, $value)->execute();
            if ($ret) {
                return $this->getCheckYes([], '型号新增成功');
            }
            return $this->getCheckNo('型号新增失败');
        }
        $id = Yii::$app->request->get('id', null);
        $brand_name = '顶层分类';
        $depth = 1;
        if ($id) {
            $model = new BrandModel();
            $brand = $model->getBrand($id);
            $depth = $brand->depth + 1;
            $brand_name=$brand->model_name;
        }

        $this->render('create', ['id' => $id, 'brand' => $brand_name, 'depth' => $depth]);
    }

    public function actionEdit(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id', 0);
            $name = Yii::$app->request->post('model_name', '');
            $sort = trim(Yii::$app->request->post('sort', 255));
            if (!$name || !$id) {
                return $this->getCheckNo('却少必须参数!');
            }
            $model = $this->findModel($id);
            if(!$model) return $this->getCheckNo('当前记录不存在');
            $model ->model_name =$name;
            $model ->sort = $sort;
            if ($model->save()) {
                return $this->getCheckYes(['url'=>$this->createUrl(['brand/index','id'=>$model->parent_id])], '型号修改成功');
            }
            return $this->getCheckNo('型号修改失败');
        }
        $id = Yii::$app->request->get('id', null);
        if (!$id) {
            $this->showMessage('访问错误');
        }
        $model = new BrandModel();
        $brand = $model->getBrand($id);
        $brand_name='顶层分类';
        if($brand->parent_id){
            $parent = $this->findModel($brand->parent_id);
            if($parent)  $brand_name = $parent ->model_name;
        }
        $this->render('edit', ['id' => $id, 'brand' => $brand_name,'model'=>$brand]);
    }

	public function actionDelete($id)
	{
		Yii::$app->cache->delete('brand_'.$id);//clear cache
		if($this->findModel(Yii::$app->request->get('id',0))->delete()){
			return $this->getCheckYes(null,'删除成功');
		}
		return $this->getCheckNo('删除失败 ');
	}
	protected function findModel($id)
	{
		if (($model = BrandModel::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}




}
