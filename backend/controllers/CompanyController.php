<?php

namespace backend\controllers;


use backend\components\LoginedController;
use common\library\helper;
use common\library\UploadFile;
use common\models\Area;
use Yii;
use common\models\InsuranceCompany;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InsuranceCompanyController implements the CRUD actions for InsuranceCompany model.
 */
class CompanyController extends LoginedController
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
     * Lists all InsuranceCompany models.
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
        $insurance_company=InsuranceCompany::find();
        $count=$insurance_company->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' =>$insurance_company->orderBy('id DESC')->limit($pageSize)->offset($start),
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
            $btn .= '&nbsp;&nbsp;&nbsp;<a class="btn btn-xs btn-success" href="'.$this->createUrl(['company/create','id'=>$val->id]).'"><i class="fa fa-edit"></i>编辑</a>';
            $data['data'][]=array(
//                '<input type="checkbox" name="id[]" value="'.$val->id.'">',
                $val->id,
                $val->name,
                $val->logo?'<img src="'.$val->logo.'" class="show_img" height="60" style="max-height:120px;max-height:60px;" >':'',
                $val->sp,
                $val->contact_name,
                $val->contact_phone,
                $val->address_detail,
                '<a href="'.$this->createUrl(['coverage/index','company_id'=>$val->id]).'">'.$val->insurance_number.'</a>',
                $val->status?'<span class="label label-sm label-success">正常</span>':'<span class="label label-sm label-danger">冻结中</span>',
                $btn
            );
        }

        return json_encode($data);
    }



    /**
     * Creates a new InsuranceCompany model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InsuranceCompany();
        if(Yii::$app->request->isPost){
            $id=Yii::$app->request->post('id',null);
            if(!is_null($id)){
                $model = InsuranceCompany::findOne(['id'=>$id]);
                if(!$model){
                    return $this->getCheckNo('当前公司ID不存在');
                }
            }

            $post=Yii::$app->request->post();
            if($post['logo']){
                $model->logo=$post['logo'];
            }
            $model->name=$post['company_name'];
            $model->sp=strtoupper($post['sp']);
            $model->contact_name=$post['concat_name'];
            $model->contact_phone=$post['concat_tel'];
            list($p_id,$province)=explode(',',$post['p_id']);
            list($c_id,$city)=explode(',',$post['c_id']);
            list($a_id,$area)=explode(',',$post['a_id']);
            $model->p_id=(int)$p_id;
            $model->c_id=(int)$c_id;
            $model->a_id=(int)$a_id;
            $model->address_detail=$province.' '.$city.' '.$area.' '.$post['address_detail'];
            $model->note=$post['note'];
            $model->status=1;
            $model->created=time();
            if($model->save()){
                return $this->getCheckYes([],'操作成功');
//                return $this->redirect($this->createUrl(['company/index']));
            }
            return $this->getCheckNo('操作失败');
//            return $this->redirect($this->createUrl(['company/create','id'=>$id,'msg'=>'操作失败']));
        }
        $company_id=Yii::$app->request->get('id',null);
        $company_info=[];
        $city_html='';
        $area_html='';
        if($company_id){
            $company_info=$model->find()->where(['id'=>$company_id])->asArray()->one();
            $area_info=explode(' ',$company_info['address_detail']);
            unset($area_info[0],$area_info[1],$area_info[2]);
            $company_info['address_detail'] =join(' ',$area_info);
            $city_html=helper::getAreaSelect($company_info['p_id'],$company_info['c_id']);
            $area_html=helper::getAreaSelect($company_info['c_id'],$company_info['a_id']);
        }
        $company_info = $this->viewData($company_info);
        $area=Area::findAll(['area_parent_id'=>0]);
        return $this->render('create', [
            'model' => $model,
            'province'=>$area,
            'info'=>$company_info,
            'city_html'=>$city_html,'area_html'=>$area_html
        ]);
    }

    /**
     * 返回默认保险公司信息资料
     * @param $seller
     * @return array
     */
    private function viewData($company){
        $company=is_array($company)?$company: [];
        $temp=['name'=>'','sp'=>'','contact_name'=>'','contact_phone'=>'','p_id'=>'','c_id'=>'','a_id'=>'',
            'address_detail'=>'','note'=>'','id'=>''];
        return array_merge($temp,$company);
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
        $company = InsuranceCompany::findOne(['id'=>$id]);
        if($company){
            $company->status = (int) $status;
            if($company->save()){
                return $this->getCheckYes([],'操作成功!');
            }
        }
        return $this->getCheckNo('操作失败!');
    }


    public function actionUpload(){
        if($_FILES['logo']['name']) {
            $upload = new UploadFile();
            $dir = 'uploads/company/';
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
     * Finds the InsuranceCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InsuranceCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InsuranceCompany::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
