<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\library\UploadFile;
use common\models\InsuranceCompany;
use common\models\InsuranceType;
use Yii;
use common\models\InsuranceCoverage;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CoverageController implements the CRUD actions for InsuranceCoverage model.
 */
class CoverageController extends LoginedController
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
     * Lists all InsuranceCoverage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model_company=InsuranceCompany::findAll(['status'=>1]);
        $model_type=InsuranceType::findAll(['status'=>1]);
        $company_id=Yii::$app->request->get('company_id','');
        $type_id=Yii::$app->request->get('type_id','');
        $data=['c_id'=>$company_id,'t_id'=>$type_id];
        return $this->render('index',['model_company'=>$model_company,'model_type'=>$model_type,'pramas'=>$data]);
    }


    /**
     * Creates a new InsuranceCoverage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InsuranceCoverage();
        $model_company=InsuranceCompany::findAll(['status'=>1]);
        $model_type=InsuranceType::findAll(['status'=>1]);
        $id=Yii::$app->request->get('id',null);
        $info=[];
        if($id){
            $info=$model->find()->where(['id'=>$id])->asArray()->one();
            $info=$this->viewData($info);
        }
        return $this->render('create',['model_company'=>$model_company,'model_type'=>$model_type,'info'=>$info]);
    }

    /**
     * 返回默认保险信息资料
     * @param $seller
     * @return array
     */
    private function viewData($company){
        $company=is_array($company)?$company: [];
        $temp=['company_id'=>'','type_id'=>'','coverage_name'=>'','period'=>'','official_price'=>'','cost_price'=>'','wholesale_price'=>'',
            'max_payment'=>'','coverage_code'=>'','id'=>'','image'=>'','is_more'=>0];
        return array_merge($temp,$company);
    }

    public function actionUpdate(){
        if(!Yii::$app->request->isPost){
            return $this->showMessage('非法访问控制器');
        }

        $post=Yii::$app->request->post();
        $id=Yii::$app->request->post('id',null);
        $model = new InsuranceCoverage();
        if(!is_null($id)){
            $model=InsuranceCoverage::findOne(['id'=>$post['id']]);
            //$old_type = $model->type_id;
            //$old_company =$model->company_id;
            if(!$model){
                return $this->getCheckNo('当前险种ID不存在');
            }
        }
		if($model->isNewRecord){
			list($company_id,$company,$sp)=explode(',',$post['company_name']);
			list($type_id,$type,$type_code)=explode(',',$post['type_name']);
			$model->company_id=$company_id;
			$model->type_id=$type_id;
			$model->type_name=$type;
			$model->company_name=$company;
			$model->period=$post['period'];
			$model->official_price=$post['official_price'];
			$model->coverage_code = $sp . str_pad($post['period'], 2, 0, STR_PAD_LEFT) . str_pad(intval($post['official_price']), 3, 0, STR_PAD_LEFT) . $type_code;
			$model->status=1;

		}

        if($post['image']){
            $model->image=$post['image'];
        }
        $model->is_more=(int)$post['is_more'];
        $model->coverage_name=$post['coverage_name'];
        $model->cost_price=$post['cost_price'];
        $model->wholesale_price=$post['wholesale_price'];
        $model->max_payment=$post['max_payment'];
		$model->add_time=time();
        $model->note=$post['note'];
        if($model->save()){
            if(is_null($id)){
                $this->_secCompany($company_id);
                $this->_secType($type_id);
            }
            return $this->getCheckYes([],'操作成功');
        }
        return $this->getCheckNo('操作失败');
    }

    private function _secCompany($company_id=0,$add='add'){
        $company_model=InsuranceCompany::findOne(['id'=>$company_id]);
        if($company_model){
            if($add =='add'){
                $company_model->insurance_number += 1;
            }else if($add =='mod'){
                $company_model->insurance_number -= 1;
            }
            $company_model->save();
        }
    }

    private function _secType($type_id=0,$add='add'){
        $type_model=InsuranceType::findOne(['id'=>$type_id]);
        if($type_model){
            if($add =='add'){
                $type_model->insurance_number +=1;
            }else if($add =='mod'){
                $type_model->insurance_number -=1;
            }
            $type_model->save();
        }
    }

    public function actionChange(){
        if(!Yii::$app->request->isAjax || !Yii::$app->request->isPost){
            return $this->getCheckNo('非法访问!');
        }
        $id =Yii::$app->request->post('id');
        $status=Yii::$app->request->post('status',null);
        if(!$id || is_null($status)){
            return $this->getCheckNo('参数错误!');
        }
        $company = InsuranceCoverage::findOne(['id'=>$id]);
        if($company){
            $company->status = (int) $status;
            if($company->save()){
                return $this->getCheckYes([],'操作成功!');
            }
        }
        return $this->getCheckNo('操作失败!');
    }


    public function actionGetdata()
    {

        $post=Yii::$app->request->post();
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $coverage=InsuranceCoverage::find();
        $tj=[];
        if(isset($post['company_id']) && $post['company_id']){
            $tj['company_id']=$post['company_id'];
        }
        if(isset($post['type_id']) && $post['type_id']){
            $tj['type_id']=$post['type_id'];
        }
        if(isset($post['status']) && $post['status']){
            $tj['status']=$post['status'] - 1 ;
        }
        if(isset($post['period']) && $post['period']){
            $tj['period']=$post['period'];
        }
        $coverage->where($tj);
        if(isset($post['coverage_code']) && $post['coverage_code']){
            $coverage->andWhere(['like','coverage_code',$post['coverage_code']]);
        }

        $count=$coverage->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' =>$coverage->orderBy('id DESC')->limit($pageSize)->offset($start),
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
            $btn = $val->status?
                '<a class="btn red btn-xs btn-default " onClick="handleStatus('.$val->id.','. 0 .')"  href="javascript:;"><i class="fa fa-caret-right"></i> 冻结 </a>'
                :'<a class="btn blue btn-xs btn-default" onClick="handleStatus('.$val->id.','. 1 .')" href="javascript:;"><i class="fa fa-check"></i> 解除冻结 </a>';
            $btn .= '&nbsp;&nbsp;&nbsp;<a class="btn btn-xs btn-success" href="'.$this->createUrl(['coverage/create','id'=>$val->id]).'"><i class="fa fa-edit"></i>编辑</a>';
            $data['data'][]=array(
//                '<input type="checkbox" name="id[]" value="'.$val->id.'">',
                $val->id,
                $val->coverage_name,
                $val->coverage_code,
                $val->company_name,
                $val->cost_price,
                $val->official_price,
                $val->type_name,
                $val->period.'月',
                $val->max_payment,
                $val->add_time?date('Y-m-d',$val->add_time):'',
                $val->status?'<span class="label label-sm label-success">正常</span>':'<span class="label label-sm label-danger">冻结中</span>',
                $btn
            );
        }

        return json_encode($data);
    }



    public function actionUpload(){
        if($_FILES['image']['name']) {
            $upload = new UploadFile();
            $dir = 'uploads/coverage/';
            $upload->maxSize = 3145728;
            $upload->savePath = $dir;
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
            if (!$upload->upload()) {// 上传错误提示错误信息
                $msg = $upload->getErrorMsg();
                return json_encode(array('status' => 0, 'msg' =>$msg));
            } else {// 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
                return json_encode(array('status' => 1, 'msg' =>'','url'=>'/'.$info[0]['savepath'].$info[0]['savename']));
            }
        }
        return json_encode(array('status' => 0, 'msg' =>'失败'));
    }

    /**
     * Finds the InsuranceCoverage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InsuranceCoverage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InsuranceCoverage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
