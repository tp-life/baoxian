<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\library\Excel;
use common\library\helper;
use common\library\UploadFile;
use common\models\BrandModel;
use common\models\CardCouponsGrant;
use common\models\InsuranceCoverage;
use common\models\OrderExtend;
use common\models\OrderLog;
use common\models\OrderMaintenance;
use common\tool\Sms;
use m35\thecsv\theCsv;
use Yii;
use common\models\Order;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends LoginedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        if (!$id) {
            $this->showMessage('访问错误');
        }
        $query = new Query();
        $order_info = $query->from(['o' => Order::tableName(), 'o_e' => OrderExtend::tableName()])->where('o.order_id = o_e.order_id')
            ->andWhere(['o.order_id' => $id])->one();
        $converage = InsuranceCoverage::find()->where(['id' => $order_info['coverage_id']])->one();
        $brand = new BrandModel();
        $brand_model = $brand->getBrand($order_info['brand_id'])->model_name . '#' .
            $brand->getBrand($order_info['model_id'])->model_name . '#' .
            $brand->getBrand($order_info['color_id'])->model_name;
        $order_model = new Order();

        $main_order = OrderMaintenance::find()->where(['order_id' => $id])->orderBy('id DESC')->all();

        return $this->render('view', [
            'order' => $order_info,
            'coverage' => $converage,
            'brand' => $brand_model,
            'status' => $order_model->getStatus($order_info),
            'order_log' => OrderLog::find()->where(['order_id' => $order_info['order_id']])->orderBy('log_id desc')->all(),
            'main_order' => $main_order
        ]);
    }

    private function _condition($condition = array())
    {
        $model = new Query();
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $post = array_merge($get, $post);
        $where = [];
        $model->from(['o' => Order::tableName()]);
		$model->leftJoin(['o_e' => OrderExtend::tableName()],'o.order_id = o_e.order_id');
		$model->leftJoin(['c' => InsuranceCoverage::tableName()],'o.coverage_id = c.id');
        $model->select('o.*,o_e.*,c.period');
        if ($post['status'] !== '') {
            if($post['status'] =='32'){//过保处理
                $model->andWhere(['<','o_e.end_time',$this->time]);
                $model->andWhere(['<>','o_e.end_time',0]);
            } else {
                $where['o.order_state'] = (int)$post['status'];
            }
        }
        if (!empty($post['date']) && !empty($post['e_date'])) {
            $t = strtotime($post['date']);
            $e = strtotime($post['e_date']);
            $model->andWhere(['between', 'o.add_time', $t, $e + 24 * 3600]);
        } else if (!empty($post['date'])) {
            $t = strtotime($post['date']);
            $model->andWhere(['>=', 'o.add_time', $t]);
        } else if (!empty($post['e_date'])) {
            $e = strtotime($post['e_date']);
            $model->andWhere(['<=', 'o.add_time', $e + 24 * 3600]);
        }

        if (isset($post['fg']) && in_array($post['fg'], ['buyer', 'buyer_phone', 'imei_code', 'brand', 'policy_number', 'seller_name', 'coverage_code', 'card_number','order_sn']) && !empty($post['search'])) {
            if (!in_array($post['fg'], ['coverage_code', 'brand', 'card_number','order_sn'])) {
                $model->andWhere(['like', 'o_e.' . $post['fg'], $post['search']]);
            } else if ($post['fg'] == 'coverage_code') {
                $where['o.coverage_code'] = $post['search'];
            } else if ($post['fg'] == 'card_number') {
                $where['o.order_id'] = 0;
                $cards = CardCouponsGrant::findOne(['card_number' => $post['search']]);
                if ($cards && $cards->order_id) {
                    $where['o.order_id'] = $cards->order_id;
                }
            }else if($post['fg'] == 'order_sn'){
                $where['o.order_sn'] = $post['search'];
            }
        }
        $condition = array_merge($where, $condition);
        $model->andWhere($condition)->orderBy(['o.add_time'=>SORT_DESC,'o.order_id' => SORT_DESC]);
        return $model;
    }


    public function actionGetdata()
    {
		$is_admin = Yii::$app->user->identity->getIsSystemRole();

        $model = $this->_condition([]);
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量

        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->limit($pageSize)->offset($start),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);

        $member = $dataProvider->getModels();

        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        $brand = new BrandModel();
        $order = new Order();

        foreach($member as $key=>$val){
            $b=$brand->getBrand($val['brand_id'])->model_name;
            $m=$brand->getBrand($val['model_id'])->model_name;
            $btn = '<a class="btn green btn-xs  btn-default" title="点击查看详细" href="' . $this->createUrl(['order/view', 'id' => $val['order_id']]) . '"><i class="fa fa-share"></i> 详细</a>';
			if($is_admin && ($val['order_state'] == Order::__ORDER_PAY || $val['order_state'] == Order::__ORDER_APPLF_ERR)){
				//管理员角色并且在完善资料或者审核失败状态可编辑订单 add by leo.yan
				$btn .= '<a class="btn purple-seance btn-xs" target="_blank" title="点击可编辑订单" href="' . $this->createUrl(['order/edit', 'id' => $val['order_id']]) . '"><i class="fa fa-pencil"></i> 编辑</a>';
			}
            if($is_admin && ($val['order_state'] == Order::__ORDER_ENSURE)){
                $main = OrderMaintenance::find()->where("order_id = :id AND  state <> :state ",[':id'=>$val['order_id'],':state'=>OrderMaintenance::_MT_STATE_FAIL])->asArray()->one();
                if(!$main){
                    $btn .= '<a class="btn purple-seance btn-xs" target="_blank" title="点击申请理赔" href="' . $this->createUrl(['order/maintainer', 'id' => $val['order_id']]) . '"> 申请理赔</a>';
                }
            }
            $data['data'][]=array(
//                '<input type="checkbox" name="id[]" value="'.$val['order_id'].'">',
                $val['order_id'],
                $val['order_sn'],
                $val['buyer'],
                $val['buyer_phone'],
                $val['imei_code'],
                $b.$m,
                $val['coverage_code'],
                $val['add_time']?date('Y-m-d H:i',$val['add_time']):'',
                '<label class="btn purple-seance btn-xs">'.$order->getStatus($val).'</label>',
                $val['policy_number'],
                $val['period'] . ' 月',
                $btn
            );
        }

        return json_encode($data);
    }


    public function actionMaintainer($id){
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
            $order_id = intval(Yii::$app->request->post('order_id',0));
            $phone_img = trim(Yii::$app->request->post('phone_img',''));
            $back_img = trim(Yii::$app->request->post('back_img',''));
            $id_back_image = trim(Yii::$app->request->post('id_back_img',''));
            $id_face_image = trim(Yii::$app->request->post('id_face_img',''));
            $mark = trim(Yii::$app->request->post('mark',''));
            if (!$order_id) {
                return $this->getCheckNo('查无保障中保单记录');
            }
            if (!is_file(ltrim($phone_img,'/'))) {
                return $this->getCheckNo('手机正面照处理失败，请重试');
            }
            if (!is_file(ltrim($back_img,'/')) ) {
                return $this->getCheckNo('手机背面照处理失败，请重试');
            }
            if (!is_file(ltrim($id_back_image,'/'))) {
                return $this->getCheckNo('身份证背面照处理失败，请重试');
            }
            if (!is_file(trim($id_face_image,'/'))) {
                return $this->getCheckNo('身份证正面照处理失败，请重试');
            }
            $condition = ['order_state' => Order::__ORDER_ENSURE, 'order_id' => $order_id];
            $order = Order::findOne($condition);
            if (!$order) {
                return $this->getCheckNo('查无保障中保单记录');
            }
            $orderExtend = OrderExtend::findOne(['order_id'=>$order['order_id']]);
            //如果审核失败 就修改
            $orderMaintainObj = OrderMaintenance::findOne(['order_id'=>$order['order_id']]);

            if($orderMaintainObj && $orderMaintainObj['state']!=OrderMaintenance::_MT_STATE_FAIL){
                return $this->getCheckYes([],'正在处理理赔申请，不能重复提交');
            }
            if(empty($orderMaintainObj)){
                $orderMaintainObj = new OrderMaintenance();
            }
            $orderMaintainObj->setAttributes([
                'member_id'=>$order['member_id'],
                'order_id'=>$order['order_id'],
                'order_sn'=>$order['order_sn'],
                'type'=>OrderMaintenance::_MT_TYPE_MAIL,//默认邮寄
                'contact'=>$orderExtend['buyer'],
                'contact_number'=>$orderExtend['buyer_phone'],
                'state'=>OrderMaintenance::_MT_STATE_TO_CHECK,//待审核
                'mark'=>$mark,
                'add_time'=>time(),
                'phone_img'=>$phone_img,
                'id_back_img'=>$id_back_image,
                'id_face_img'=>$id_face_image,
                'back_img'=>$back_img
            ]);
            if($orderMaintainObj->save(false)){
                return $this->getCheckYes(['url'=>$this->createUrl(['ordermainten/view','id'=>$orderMaintainObj->id])],'理赔申请成功，待处理');
            }
            return $this->getCheckNo('理赔申请失败');

        }
        $query=new Query();
        $order_info=$query->from(['o'=>Order::tableName(),'o_e'=>OrderExtend::tableName()])->where('o.order_id = o_e.order_id')
            ->andWhere(['o.order_id'=>$id])->one();
        $main = OrderMaintenance::find()->where("order_id = :id AND  state <> :state ",[':id'=>$id,':state'=>OrderMaintenance::_MT_STATE_FAIL])->asArray()->one();
        if($order_info['order_state'] != Order::__ORDER_ENSURE && $main){
            $this->showMessage('此订单状态不支持理赔','提示信息','info',Url::to(['order/index']));
        }
        $brand = new BrandModel();
        $converage=InsuranceCoverage::find()->where(['id'=>$order_info['coverage_id']])->one();
        $b=$brand->getBrand($order_info['brand_id'])->model_name;
        $m=$brand->getBrand($order_info['model_id'])->model_name;
        $order_model=new Order();
        return $this->render('main', [
            'order'=>$order_info,
            'coverage'=>$converage,
            'status'=>$order_model->getStatus($order_info),
            'brand'=>$b.' '.$m
        ]);
    }

    /**
     * 订单审核
     * @return array
     */
    public function actionChangorder()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法访问');
        }
        $id = Yii::$app->request->post('order_id', '');
        $text = Yii::$app->request->post('text', '');
        $status = Yii::$app->request->post('status', '');
        $err_status = Yii::$app->request->post('err_status', 0);
        if (!$id || !$status) {
            return $this->getCheckNo('参数错误');
        }
        $tran = Yii::$app->db->beginTransaction();
        try {
            $order_extend = OrderExtend::findOne(['order_id' => $id]);
            $model = Order::findOne(['order_id' => $id]);
            if ($status == 1) {
                //检测imei号是否重复提交
                $imei = $order_extend->imei_code;
                if ($imei) {
                    $query = new Query();
                    $count = $query->from(['o' => Order::tableName(), 'o_e' => OrderExtend::tableName()])
                        ->where('o.order_id = o_e.order_id and ( ( o.order_state = 30 and o_e.end_time > :time ) or o.order_state = 22 ) and o.order_id <> :order_id and o_e.imei_code = :imei',
                            [':time' => time(), ':order_id' => $id, ':imei' => $imei])
                        ->count();
                    if ($count) {
                        throw new Exception('当前imei号当前已经投保.审核失败.');
                    }
                }
                $coverage = InsuranceCoverage::findOne(['id' => $model->coverage_id]);
                if (!$coverage) {
                    throw  new Exception('当前险种信息不存在！');
                }
                $state = Order::__ORDER_APPLF_SUCCESS;
                $now_time = strtotime(date('Y-m-d'));
                $order_extend->start_time = strtotime('+8 day', $now_time);
                $order_extend->end_time = strtotime('+' . $coverage->period . ' month', $order_extend->start_time) - 1 ;
            } else {
                $state = Order::__ORDER_APPLF_ERR;
                $order_extend->err_code = $err_status;
            }
            $model->order_state = $state;
            if (!$model->save() || !$order_extend->save()) {
                throw new Exception('审核失败');
            }
            if ($state == Order::__ORDER_APPLF_SUCCESS) {
                helper::sendSms('applfSuccess', ['tel' => $order_extend->buyer_phone, 'order_sn' => $model->order_sn, 'start' => date('Y-m-d',$order_extend->start_time), 'end' => date('Y-m-d',$order_extend->end_time)], Sms::TYPE_ORDER);
            }else if ($state == Order::__ORDER_APPLF_ERR) {
                if ($err_status == Order::__ERR_PHONE) {
                    $text .= '(' . Order::errMsg(Order::__ERR_PHONE) . ') ';
                    helper::sendSms('phoneErr', ['tel' => $order_extend->buyer_phone], Sms::TYPE_ORDER);
                } else if ($err_status == Order::__ERR_IMEI) {
                    $text .= '(' . Order::errMsg(Order::__ERR_IMEI) . ') ';
                    helper::sendSms('imeiErr', ['tel' => $order_extend->buyer_phone], Sms::TYPE_ORDER);
                } elseif ($err_status == Order::__ERR_BRAND) {
                    $text .= '(' . Order::errMsg(Order::__ERR_BRAND) . ') ';
                    helper::sendSms('brandErr', ['tel' => $order_extend->buyer_phone], Sms::TYPE_ORDER);
                }
            }
            $log = [
                'before_order_state' => Order::__ORDER_APPLF,
                'order_state' => $state,
                'log_msg' => $text,
                'order_id' => $id,
                'log_user' => Yii::$app->user->identity->username,
                'log_time' => date('Y-m-d H:i:s', time())
            ];
            $model_log = new OrderLog();
            $model_log->insertLog($log);
            $tran->commit();
            return $this->getCheckYes([], '操作成功');
        } catch (Exception $e) {
            $tran->rollBack();
            return $this->getCheckNo($e->getMessage());
        }
    }


    /**
     * 更新保单号
     */
    public function actionUpdatecoverage()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法访问');
        }
        $id = Yii::$app->request->post('order_id', '');
        $number = Yii::$app->request->post('number', '');
        if (!$id  || !$number) {
            return $this->getCheckNo('参数错误');
        }
        if (empty($number) || mb_strlen($number) < 10 || mb_strlen($number) > 40) {
            return $this->getCheckNo('请输入有效保单号在10-40位');
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order_model = Order::findOne(['order_id' => $id]);
            $coverage_code = $order_model->coverage_id;
            $coverage = InsuranceCoverage::findOne(['id' => $coverage_code]);
            if (!$coverage) {
                throw new Exception('当前保险不存在');
            }
            $order_model->order_state = Order::__ORDER_ENSURE;
            $t = $order_model->save();
            $order_extend_model = OrderExtend::findOne(['order_id' => $id]);
            $order_extend_model->policy_number = $number;
            $t1 = $order_extend_model->save();
            if ($t1 && $t) {
                $transaction->commit();
                $log = [
                    'before_order_state' => Order::__ORDER_APPLF_SUCCESS,
                    'order_state' => Order::__ORDER_ENSURE,
                    'log_msg' => '更新保单号成功:' . $number,
                    'order_id' => $id,
                    'log_user' => Yii::$app->user->identity->username,
                    'log_time' => date('Y-m-d H:i:s', time())
                ];
                $model_log = new OrderLog();
                $model_log->insertLog($log);
                return $this->getCheckYes([], '更新保单号成功');
            }
            throw  new Exception('更新保单号失败');
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->getCheckNo($e->getMessage());
        }

    }


    public function actionImport()
    {
        if (!$_FILES['baoxian']['name']) {
            $this->showMessage('非法访问');
        }
        if ($_FILES['baoxian']['name']) {
            set_time_limit(0);
            $maxSize = 524880;
            $allowExts = array('csv');
            $path = pathinfo($_FILES['baoxian']['name']);

            if (!in_array($path['extension'], $allowExts) || $_FILES['offer']['size'] > $maxSize) {
                $err = '文件大小超出限制或文件后缀不合法';
                return json_encode(array('status' => 0, 'msg' => $err));
            }
            $data = $this->handData($_FILES['baoxian']['tmp_name']);
            if (!$data) {
                $err = '文件内容读取错误或者内容不合法';
                return json_encode(array('status' => 0, 'msg' => $err));
            }
            $ids = array_column($data, 'imei');
//            $result = Order::find()->where(['order_sn' => $ids, 'order_state' => Order::__ORDER_APPLF_SUCCESS])->all();
            $result = OrderExtend::find()->where(['imei_code' => $ids])->all();
            if (!$result) {
                return json_encode(array('status' => 0, 'msg' => '导入订单错误，当前导入订单不存在'));
            }
            $diff_order = [];
            $result_array = [];

            foreach ($result as $val) {
                if (array_key_exists($val->imei_code, $data)) {
                    if($val->policy_number) continue;
                    $tran = Yii::$app->db->beginTransaction();
                    $result_array[] = $val->imei_code;
                    try {
                        $model = Order::find()->where(['order_id' => $val->order_id, 'order_state' => [Order::__ORDER_APPLF_SUCCESS,Order::__ORDER_ENSURE]])->one();
                        if(!$model){
                            throw  new Exception('当前订单状态不正确');
                        }
                        $model->order_state = Order::__ORDER_ENSURE;
                        $val->policy_number = $data[$val->imei_code]['baoxian'];
                        $val->start_time = $data[$val->imei_code]['start'];
                        $val->end_time = $data[$val->imei_code]['end'];
                        $ret = $val->save();
                        if (!$model->save() || !$ret) {
                            throw  new Exception('保存失败');
                        }
                        $log = [
                            'before_order_state' => Order::__ORDER_APPLF_SUCCESS,
                            'order_state' => Order::__ORDER_ENSURE,
                            'log_msg' => '更新保单号成功:' . $val->policy_number,
                            'order_id' => $val->order_id,
                            'log_user' => Yii::$app->user->identity->username,
                            'log_time' => date('Y-m-d H:i:s', time())
                        ];
                        $tran->commit();
                        $model_log = new OrderLog();
                        $model_log->insertLog($log);
                    } catch (Exception $e) {
                        $tran->rollBack();
                        $diff_order[] = $val->imei_code;
                    }
                    usleep(8);
                }
            }
            if (count($result_array) == count($diff_order)) {
                return json_encode(array('status' => 0, 'msg' => '保险单号导入失败'));
            } else if (!count($diff_order)) {
                return json_encode(array('status' => 1, 'msg' => '保险单号导入成功'));
            } else {
                return json_encode(array('status' => 1, 'msg' => '保险单号部分导入成功'));
            }
        }
    }


    public function actionExample(){

        $temp[]=['1111111111111','22222222222222','2016-11-11','2017-11-10'];
        theCsv::export([
            'data' => $temp,
            'name' => "import_example.csv",    // 自定义导出文件名称
            'header' => ['IMEI号','保险单号','开始日期','结束日期'],
        ]);
    }

    private function handData($file = '')
    {
        $data = array();
        $handle = fopen($file, "rb");
        while (!feof($handle)) {
            $line = fgets($handle);
            $line = trim($line);
            if ($line) {
                if (!preg_match("/^[\w,\/-]*$/", $line)) continue;
                list($order_sn, $baoxian, $start, $end) = explode(',', $line);
                if (empty($baoxian) || mb_strlen($baoxian) < 10 || mb_strlen($baoxian) > 40) {
                    continue;
                }
                $data[$order_sn] = ['imei' => $order_sn, 'baoxian' => $baoxian, 'start' => strtotime($start), 'end' => strtotime($end)];
            }
            usleep(20);
        }
        return $data;
    }


    public function actionExport()
    {
        set_time_limit(0);
        $model = $this->_condition()->orderBy('o.add_time DESC,o.order_id DESC');
        $count = $model->count('o.order_id');
        if($count>5000){
			$this->showMessage('当前订单导出条件超过5000限制','警告提示','danger','javascript:window.close();');return ;
		}
        $data = $model->orderBy('o.add_time DESC,o.order_id DESC')->limit(5000)->all();
        $this->createExport($data);
    }

    /**
     * 客服备注
     * @return array
     */
    public function actionRemark()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法访问');
        }
        $id = Yii::$app->request->post('order_id', '');
        $text = Yii::$app->request->post('text', '');
        if (!$id) {
            return $this->getCheckNo('参数错误');
        }
        $model = OrderExtend::findOne(['order_id' => $id]);
        $model->server_mark = $text;
        if ($model->save()) {
            return $this->getCheckYes([], '备注成功');
        }
        return $this->getCheckNo('备注失败');
    }

    /**
     * 图片上传
     * @return string
     */
    public function actionUpload()
    {
        $data = $_REQUEST['data'];
        $id = $data['order_id'];
        $tag = $data['tag'];
        $path = $data['path'];
        if (!$id || !$tag || !$path) {
            exit(json_encode(['code' => 400, 'message' => '上传处理失败', 'data' => []]));
        }
        $model = OrderExtend::findOne(['order_id' => $id]);
        $model->$tag = $path;
        if ($model->save()) {
            exit(json_encode(['code' => 200, 'message' => 'Success', 'data' => ['path' => $path]]));
        } else {
            exit(json_encode(['code' => 400, 'message' => '上传处理失败', 'data' => []]));
        }

    }

//    public function actionUpload(){
//        $id=Yii::$app->request->post('order_id','');
//        $tag=Yii::$app->request->post('tag','');
//        if(!$id || !$tag){
//            return json_encode(array('status' => 0, 'msg' =>'失败'));
//        }
//        if($_FILES['img']['name']) {
//            $upload = new UploadFile();
//            $dir = 'uploads/coverage/';
//            $upload->maxSize = 3145728;
//            $upload->savePath = $dir;
//            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
//            if (!$upload->upload()) {// 上传错误提示错误信息
//                $msg = $upload->getErrorMsg();
//                return json_encode(array('status' => 0, 'msg' =>$msg));
//            } else {
//                $info = $upload->getUploadFileInfo();
//                $model=OrderExtend::findOne(['order_id'=>$id]);
//                $model->$tag='/'.$info[0]['savepath'].$info[0]['savename'];
//                $model->save();
//                return json_encode(array('status' => 1, 'msg' =>'','url'=>'/'.$info[0]['savepath'].$info[0]['savename']));
//            }
//        }
//        return json_encode(array('status' => 0, 'msg' =>'失败'));
//    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
			if(!Yii::$app->request->isAjax){
				$this->showMessage('查无订单记录','提示信息','info',Url::to(['order/index']));
			}else{
				return false;
			}
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    private function  createExport($data = []){
        $temp=[];
        $brand_model = new BrandModel();
        $order_model = new Order();
        foreach ($data as $order_info){
            $temp[]=[
            	date('Y-m-d',$order_info['add_time']),
                empty($order_info['start_time'])?'':date("Y-m-d",strtotime('-8 day',$order_info['start_time'])),
                empty($order_info['start_time'])?'':date("Y-m-d",$order_info['start_time']),
                $order_info['buyer_phone'],
                $order_info['buyer'],
                $brand_model->getBrand($order_info['brand_id'])->model_name,
                $brand_model->getBrand($order_info['model_id'])->model_name,
                '="'.$order_info['imei_code'].'"',
                $order_info['order_amount'],
                '="'.$order_info['policy_number'].'"',
                empty($order_info['start_time'])?'':date("Y-m-d H:i:s",$order_info['start_time']),
                empty($order_info['end_time'])?'':date("Y-m-d H:i:s",$order_info['end_time']),
                '="'.$order_info['order_sn'].'"',
                $order_model->getStatus($order_info),
                $order_info['coverage_code'],
                $order_info['coverage_name'],
                $order_info['seller_name'],
                '="'.$order_info['idcrad'].'"'
            ];
        }
        theCsv::export([
            'data' => $temp,
            'name' => "insurace-order-".date('Y_m_d_H', time()).".csv",    // 自定义导出文件名称
            'header' => ['下单日期','审核日期','生效日期','电话','姓名','手机品牌','手机型号','IMEI号','投保价','保单号','保单开始日期','保单结束日期','订单编号','订单状态','保险险种','保险名称','商家名称','身份证'],
        ]);
    }

/**
	 * [_csrf-backend] => cVdndWJzN1YAODVCCyRGOxQvKUNaQUIJATYJDxoST2YVBAEFGAtjIA==
	 * [order_id] => 88
	 * [brand_id] => 215,HTC
	 * [model_id] => 507,826D
	 * [buyer] => 志威
	 * [buyer_phone] => 18612178246
	 * [imei_code] => 234242342424242442
	 * [idcrad] => 510283188011098355
	 * [imei_face_image] => /uploads/coverage/20161221/_9e0acecd616533eb24752a1aef59a3be7567e40f.jpg
	 * [imei_back_image] => /uploads/coverage/20161221/_b88cbe8869cbfcfbe6121de7bf8ecf8036837624.jpg
	 */
	public function actionEdit($id)
	{

		//处理编辑订单
		if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
			$post = Yii::$app->request->post();
			$model = $this->findModel(Yii::$app->request->post('order_id'));
			if(!$model || ($model['order_state'] != Order::__ORDER_PAY && $model['order_state'] != Order::__ORDER_APPLF_ERR)){
				return $this->getCheckNo('此状态更新订单无效');
			}
			$order_extend = OrderExtend::findOne(['order_id'=>$model['order_id']]);
			$order_extend->attributes = [
				'buyer' => $post['buyer'],
				'buyer_phone' => $post['buyer_phone'],
				'imei_code' => $post['imei_code'],
				'idcrad' => $post['idcrad'],
				'imei_face_image' => $post['imei_face_image'],
				'imei_back_image' => $post['imei_back_image'],
				'brand_id' => explode(',', $post['brand_id'])[0],
				'model_id' => explode(',', $post['model_id'])[0],
				'is_data' => 1
			];
			if($order_extend->update(false,['buyer','buyer_phone','imei_code','idcrad','imei_face_image','imei_back_image','brand_id','model_id','is_data'])){
				$model->order_state = Order::__ORDER_APPLF;
				$model->update(false,['order_state']);
				return $this->getCheckYes([],'处理成功');
			}
			return $this->getCheckNo('订单编辑异常');

		}
		$query=new Query();
		$order_info=$query->from(['o'=>Order::tableName(),'o_e'=>OrderExtend::tableName()])->where('o.order_id = o_e.order_id')
			->andWhere(['o.order_id'=>$id])->one();
		if($order_info['order_state'] != Order::__ORDER_PAY && $order_info['order_state'] != Order::__ORDER_APPLF_ERR){
			$this->showMessage('此订单状态不支持编辑','提示信息','info',Url::to(['order/index']));
		}

		$converage=InsuranceCoverage::find()->where(['id'=>$order_info['coverage_id']])->one();

		$order_model=new Order();
		$brand = BrandModel::findAll(['parent_id' => 0]);
		$model_html = helper::getBrandModel($order_info['brand_id'], $order_info['model_id']);
		return $this->render('edit', [
			'order'=>$order_info,
			'coverage'=>$converage,
			'status'=>$order_model->getStatus($order_info),
			'brand' => $brand, 'model_html' => $model_html
		]);


	}
}
