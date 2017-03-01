<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\CardCouponsGrant;
use common\models\CardGrantRelation;
use common\models\CardOrderItem;
use common\models\CardOrderItemLog;
use common\models\CardOrderPayback;
use common\models\CardRefund;
use common\models\CardRefundLog;
use common\models\Seller;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

class FinanceController extends LoginedController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetdata(){
        $pageSize = Yii::$app->request->post('length', 10);
        $start = Yii::$app->request->post('start', 0);//偏移量
        $model =$this->_condition();
        $count = $model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' => $model->orderBy('pay_status ASC,add_time DESC')->limit($pageSize)->offset($start),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);
        $data = [
            'draw' => intval($_REQUEST['draw']),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => []
        ];
        $result = $dataProvider->getModels();
        foreach($result as $val){

            $seller_name=Seller::getSellerInfo($val->to_seller_id)->seller_name;
			$btn = '<a class="btn green btn-xs  btn-default" data-target="#my-card-apply" data-toggle="modal"  href="' . $this->createUrl(['card/info', 'pay_sn' => $val->pay_sn]) . '"><i class="fa fa-share"></i> 查看详细</a>';
            $wait_send = \common\models\CardRefund::getWaitSendPrice($val->pay_sn);
            $price = $val->send_total_price +$wait_send - $val->received_price - $val->back_price + $val->real_back_price;
            if($price <= 0){
                $price = 0.00;
            }
            if(in_array($val->pay_status,[0,1,2])) {
                $btn .= '<button type="button" class="btn btn-default  btn-outline purple-studio" onclick="handleMoney(' . $val->pay_id . ',\'' . $price . '\',\'' . $val->received_price . '\',' . $val->apply_type .',\'' .($val->send_total_price +$wait_send).'\',\''.$wait_send.'\',\'' .$val->back_price. '\')">回款</button>';
            }
            $deadline=CardGrantRelation::getDeadline($val->pay_sn);
            if($deadline  && $deadline < time() && !in_array($val->pay_status,[3,4])  ){
                $btn.='<button type="button" class="btn btn-xs  btn-outline red-thunderbird" onclick="handleChange('.$val->pay_id.')">作废订单</button>';
            }
            $data['data'][]=[
                $val->pay_id,
                $seller_name,
                $val->pay_sn,
                '<span class="font-purple-seance">'.$val->num.'</span>',
                '<span class="font-purple-seance"> ¥ '.($val->send_total_price +$wait_send).'</span>',
                '<span class="font-purple-seance"> ¥ '.$val->back_price.'</span>',
                '<span class="font-purple-seance"> ¥ '.$price.'</span>',
                '<span class="font-purple-seance"> ¥ '.$val->received_price.'</span>',
                '<span class="font-red-flamingo">'.CardOrderPayback::getMsg($val->pay_status).'</span>',
                date('Y-m-d H:i:s',$val->add_time),
                $btn
            ];
        }
        echo json_encode($data);
        exit;
    }

    /**
     * 收款
     * @return array
     */
    public function actionReceipt(){
        if(!Yii::$app->request->isAjax || !Yii::$app->request->isPost){
            $this->showMessage('非法访问');
        }
        $post = Yii::$app->request->post();
        if(!$post['pay_id'] || !$post['pay_status']){
            return $this->getCheckNo('参数错误！');
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $model = CardOrderPayback::findOne(['pay_id'=>$post['pay_id']]);
            $model->pay_status = $post['pay_status'];
            $model ->received_price = $post['actual'];
            $ret=$model->save();
            if(!$ret){
                throw  new Exception('收款失败');
            }
            if($post['pay_status'] == CardOrderPayback::ALL_RETURN_MONEY){
                $count = CardGrantRelation::find()->where(['pay_sn'=>$model->pay_sn])->andWhere(['>','deadline',0])->count();
                if($count && CardGrantRelation::updateAll(['deadline'=>0],['pay_sn'=>$model->pay_sn]) != $count){
                    throw  new Exception('收款取消时间限制失败');
                }
            }
            $tran->commit();
            return $this->getCheckYes([],'回款成功！');
        }catch (Exception $e){
            $tran->rollBack();
            return $this->getCheckNo($e->getMessage());
        }
    }

    /**
     * 作废订单
     */
    public  function actionCancel(){
        if(!Yii::$app->request->isAjax || !Yii::$app->request->isPost){
            $this->showMessage('非法访问');
        }
        $pay_id = Yii::$app->request->post('pay_id','');
        if(!$pay_id) return $this->getCheckNo('参数错误！');
        $transaction = Yii::$app->getDb()->beginTransaction();
        try{
            $model = CardOrderPayback::findOne(['pay_id'=>$pay_id]);
            $model->pay_status = 4;
            $st1=$model->save();
            $result = CardGrantRelation::getOrderCard($model->pay_sn);
            $cards = array_column($result,'card_id');
            $st2 = CardCouponsGrant::cast($cards);
            if($st1 && $st2){
                $item = CardOrderItem::find()->where(['pay_sn'=>$model->pay_sn])->asArray()->all();
                foreach($item as $v){
                    $log = ['order_id'=>$v['order_id'],'content'=>'财务取消该批次订单'];
                    CardOrderItemLog::addLog($log);
                }
                $transaction->commit();
                return $this->getCheckYes([],'作废成功');
            }
            throw new Exception('操作失败');
        }catch (Exception $e){
            $transaction->rollBack($e->getMessage());
        }
    }

    /**
     * 财务退款
     */
    public function actionRefund(){
        if(Yii::$app->request->isAjax){
            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量
            $model =$this->_refundCondition();
            $count = $model->count('*');
            $dataProvider = new ActiveDataProvider([
                'query' => $model->orderBy('status ASC,id DESC')->limit($pageSize)->offset($start),
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page' => intval($start / $pageSize),
                    'totalCount' => $count
                ]
            ]);
            $data = [
                'draw' => intval($_REQUEST['draw']),
                'recordsTotal' => $count,
                'recordsFiltered' => $count,
                'data' => []
            ];
            $result = $dataProvider->getModels();
            foreach ($result as $val){
                $seller_name=Seller::getSellerInfo($val->from_seller_id)->seller_name;
                $pay_info= CardOrderPayback::findOne(['pay_sn'=>$val->pay_sn]);
                $wait_send = CardRefund::getWaitSendPrice($val->pay_sn);
                $sy=$pay_info->send_total_price + $wait_send - $pay_info ->received_price - $pay_info->back_price + $pay_info->real_back_price;
                $btn = '<a class="btn green btn-xs  btn-default"  href="' . $this->createUrl(['finance/view', 'id' => $val->id]) . '"><i class="fa fa-share"></i> 查看详细</a>';
                if($val->status == CardRefund::_RF_STATE_SUCCESS){
                    $p = $val->real_back_price;
                }else{
                    $p=$sy >= 0?'0.00':abs($sy);
                }
                $data['data'][]=[
                    $val->id,
                    $seller_name,
                    $val->pay_sn,
                    '<span class="font-red-thunderbird"> ￥ '. $pay_info -> send_total_price.'</span>',
                    '<span class="font-red-thunderbird"> ￥ '.$pay_info -> received_price.'</span>',
                    $val->coverage_code,
                    $val->number - $val->issue_number,
                    '<span class="font-red-thunderbird"> ￥ '.$p.'</span>',
                    '<span class="font-purple-seance ">'.$val->getStatusText().'</span>',
                    date('Y-m-d H:i:s',$val->add_time),
                    $btn
                ];
            }
            echo json_encode($data);
            exit;
        }
        $this->render('refund');
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $pay_info = CardOrderPayback::findOne(['pay_sn'=>$model->pay_sn]);
        return $this->render('refund_view', ['model' => $model,'pay_info'=>$pay_info]);
    }

    public function actionCardrefund(){
        if(!Yii::$app->request->isAjax || !Yii::$app->request->isPost){
            $this->showMessage('非法访问');
        }
        $post= Yii::$app->request->post();
        if(!$post['refund_id']){
            return $this->getCheckNo('参数错误');
        }
        $model = $this->findModel($post['refund_id']);
        if(!$model){
            return $this->getCheckNo('当前退款订单不存在');
        }
        $transaction = Yii::$app->getDb()->beginTransaction();

        try{
            $price = (float)$post['real_price'];
            $model -> status = CardRefund::_RF_STATE_SUCCESS;
            $model ->real_back_price = $price;
            $ret=$model->save();
            if($ret){
                $note= $post['refund_content']?$post['refund_content'].'实际退款：'.$price:'财务确认退款。实际退款金额：'.$price;
                CardRefundLog::addLog($model->id,$note);
                $pay_model = CardOrderPayback::findOne(['pay_sn'=>$model->pay_sn]);
                $wait_send_price = CardRefund::getWaitSendPrice($model->pay_sn);
                if($pay_model ->send_total_price + $wait_send_price - $pay_model->received_price - $pay_model->back_price + $pay_model->real_back_price  <= 0 && in_array($pay_model->pay_status,[CardOrderPayback::WAIT_ISSUE,CardOrderPayback::PART_RETURN_MONEY])){
                    $pay_model ->pay_status = CardOrderPayback::ALL_RETURN_MONEY;
                }
                $pay_model ->real_back_price += $price;
                $ret2 = $pay_model->save();
                $card_numbers=explode(',',$model->card_numbers);
                $issue_cards = explode(',',$model->issue_card_numbers);
                $ret4 =true;
                if($model->issue_card_numbers){
                    $card_numbers = array_diff($card_numbers,$issue_cards);
                    $ret4 = CardCouponsGrant::changeStatus($issue_cards,CardCouponsGrant::__STATUS_DEFAULT);
                }
                $ret3 = CardCouponsGrant::changeSellerStatus($card_numbers,CardCouponsGrant::__STATUS_DEFAULT);
                if($ret2 && $ret3 && $ret4){
                    $transaction->commit();
                    return $this->getCheckYes([],'退卡成功');
                }
            }
            throw new Exception('退卡确认失败');
        }catch (Exception $e){
            $transaction->rollBack();
            return $this->getCheckNo($e->getMessage());
        }
    }

    /**
     * 收款条件查询
     * @param array $condition
     * @return \yii\db\ActiveQuery
     */
    private function _condition($condition=[]){
        $model = CardOrderPayback::find();
        $where=['from_seller_id'=>1];
        $post = Yii::$app->request->post();
        if(isset($post['status']) && $post['status'] !==''){
            $where['pay_status'] =(int) $post['status'];
        }
        if(isset($post['keyword']) && $post['keyword']){
            $child = Seller::find()->select('seller_id')->where(['like','seller_name',$post['keyword']])->asArray()->all();
            if($child){
                $to_seller_id =array_column($child,'seller_id');
                $where['to_seller_id'] = $to_seller_id;
            }else{
                $where['to_seller_id'] = -1;
            }
        }
        $condition=array_merge($where,$condition);
        $model->where($condition);
        return $model;
    }

    /**
     * 商家退卡条件查询
     * @param array $condition
     * @return \yii\db\ActiveQuery
     */
    private function _refundCondition($condition=[]){
        $model = CardRefund::find();
        $where=['to_seller_id'=>Seller::$lehuanxin,'status'=>[CardRefund::_RF_STATE_TO_WAIT,CardRefund::_RF_STATE_SUCCESS]];
        $post = Yii::$app->request->post();
        if(isset($post['status']) && $post['status'] !==''){
            $where['status'] =(int) $post['status'];
        }
        if(isset($post['keyword']) && $post['keyword']){
            if($post['type'] ==1){
                $where['pay_sn']=$post['keyword'];
            }else{
                $child = Seller::find()->select('seller_id')->where(['like','seller_name',$post['keyword']])->asArray()->all();
                if($child){
                    $from_seller_id =array_column($child,'seller_id');
                    $where['from_seller_id'] = $from_seller_id;
                }else{
                    $where['from_seller_id'] = -1;
                }
            }

        }
        $condition=array_merge($where,$condition);
        $model->where($condition);
        return $model;
    }

    protected function findModel($id)
    {
        if (($model = CardRefund::findOne($id)) !== null) {
            return $model;
        } else {
            if(Yii::$app->request->isAjax){
                return $this->getCheckNo('查无维保记录');
            }
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
