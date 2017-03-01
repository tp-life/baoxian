<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\Article;
use Yii;
use common\models\InsuranceType;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InsuranceTypeController implements the CRUD actions for InsuranceType model.
 */
class InsurancetypeController extends LoginedController
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
     * Lists all InsuranceType models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetdata()
    {

        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $insurance_type=InsuranceType::find();
        $count=$insurance_type->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' =>$insurance_type->orderBy('id desc')->limit($pageSize)->offset($start),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);

        $member = $dataProvider->getModels();

        $data=[
            'draw'=>intval($_REQUEST['draw']),
            'recordsTotal'=>$count,
            'recordsFiltered'=>$count,
            'data'=>[]
        ];
        foreach($member as $key=>$val){
            $btn='<a class="btn green btn-xs btn-default" title="点击查看" data-target="#my-card-type" href="'.$this->createUrl(['insurancetype/view','type_id'=>$val->id]).'" data-toggle="modal"><i class="fa fa-share"></i>查看详细</a>';
            $btn .= $val->status?
                '<a class="btn red btn-xs btn-default " onClick="handleStatus('.$val->id.','. 0 .')"  href="javascript:;"><i class="fa fa-caret-right"></i> 冻结 </a>'
                :'<a class="btn btn-xs default btn-editable" onClick="handleStatus('.$val->id.','. 1 .')" href="javascript:;"><i class="fa fa-check"></i> 解除 </a>';

            $data['data'][]=array(
//                '<input type="checkbox" name="id[]" value="'.$val->id.'">',
                $val->id,
                $val->type_name,
                $val->type_code,
                '<a href="'.$this->createUrl(['coverage/index','type_id'=>$val->id]).'">'.$val->insurance_number.'</a>',
                $val->status?'<span class="font-green-sharp">正常</span>':'<span class="font-red-thunderbird">冻结</span>',
                $btn
            );
        }

        return json_encode($data);
    }




    public function actionChange(){
        if(!Yii::$app->request->isAjax || !Yii::$app->request->isPost){
            return $this->getCheckNo('非法访问!');
        }
        $seller_id =Yii::$app->request->post('id');
        $status=Yii::$app->request->post('status',null);
        if(!$seller_id || is_null($status)){
            return $this->getCheckNo('参数错误!');
        }
        $seller = InsuranceType::findOne(['id'=>$seller_id]);
        if($seller){
            $seller->status = (int) $status;
            if($seller->save()){
                return $this->getCheckYes([],'操作成功!');
            }
        }
        return $this->getCheckNo('操作失败!');
    }



    /**
     * Creates a new InsuranceType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InsuranceType();
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
            $data=Yii::$app->request->post();
            $model->type_name=$data['type_name'];
            $model->type_code=$data['type_code'];
            $model->note=$data['note'];
            $model->created=time();
            if($model->save()){
                return $this->getCheckYes([],'新增成功');
            }

            return $this->getCheckNo('新增失败');
        }
        $code=$model->find()->orderBy('id desc')->one();
        $code_num=$code->type_code+1;
        return $this->render('create',['code'=>strlen($code_num) < 2?'0'.$code_num:$code_num]);
    }


    public function actionView(){
        $tid = Yii::$app->request->get('type_id','');
        if(!$tid){
            $this->showMessage('参数错误');
        }
        $info=InsuranceType::findOne(['id'=>$tid]);
        $article_info=Article::find()->select('tag_id,coverage_type_id,content')->where(['tag_id'=>[Article::COVEAGE_INFO,Article::COVEAGE_INSUE,Article::COVEAGE_CLAIMS],'status'=>1,'coverage_type_id'=>$tid])->asArray()->all();
        $view = ['info'=>'','claims'=>'','insure'=>''];
        if($article_info){
            foreach ($article_info as $val){
                if($val['tag_id'] == Article::COVEAGE_INFO){
                    $view['info'] = $val['content'];
                }elseif ($val['tag_id'] == Article::COVEAGE_INSUE){
                    $view['insure'] = $val['content'];
                }elseif ($val['tag_id'] == Article::COVEAGE_CLAIMS){
                    $view['claims'] = $val['content'];
                }
            }
        }
        return $this->renderPartial('view',['info'=>$info,'view'=>$view]);
    }


    /**
     * Finds the InsuranceType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InsuranceType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InsuranceType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
