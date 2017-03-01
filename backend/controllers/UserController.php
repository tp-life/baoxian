<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\Member;
use common\models\MemberExtend;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends LoginedController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('table');
    }

    /**
     * 获取会员列表
     * @return string
     */
    public function actionGetdata(){


        $filed=Yii::$app->request->post('field','');
        $val=Yii::$app->request->post('field_value','');
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $user=Member::find()->where(['state'=>1]);
        if($filed && $val){
            $user->andWhere(['like',$filed,$val]);
        }
        $count=$user->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' =>$user->orderBy('member_id desc')->limit($pageSize)->offset($start),
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
            $data['data'][]=array(
//                '<input type="checkbox" name="id[]" value="'.$val->member_id.'">',
                $val->member_id,
                $val->avatar?'<span class="photo"><img src="'.$val->avatar.'" class="img-circle" alt=""> </span>':'',
                $val->name,
                $val->phone,
                $val->state==1?'<span class="label label-sm label-success">开启</span>':'<span class="label label-sm label-danger">关闭</span>',
                ''
            );
        }

        return json_encode($data);
    }


    /**
     * 验证手机号码
     * @param $phone
     */
    public function actionCheckphone($phone){
        if(!$phone){
            echo 'false';
            exit;
        }
        $check=Member::findOne(['phone'=>$phone]);
        echo $check?'false':'true';
    }


    /**
     * 新增用户
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Member();
        if (Yii::$app->request->isPost) {
            $map=Yii::$app->request->post();
            $phone=$map['name'];
            $check=Member::findOne(['phone'=>$phone]);
            if($check){
                return $this->render('/seller/create', ['msg'=>'当前账户存在,请重新添加']);
            }
            $model->setPassword($map['password']);
            $model->name=$phone;
            $model->phone=$phone;
            if($model ->validate()){
                if($model->save()) {
                    $member_extend=new MemberExtend();
                    $member_extend->member_id=$model->member_id;
                    $member_extend->register_time=time();
                    $member_extend->save();
                    return $this->redirect(['/seller/perfect', 'member_id' => $model->member_id]);
                }
            }
            return $this->render('/seller/create', ['msg'=>'新增失败,请重新添加']);
        }
        return $this->render('/seller/create');
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
