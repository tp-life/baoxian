<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 2016/10/24
 * Time: 上午10:37
 */

namespace maintainer\controllers;


use common\library\helper;
use common\models\CardCouponsGrant;
use common\models\CardGrantRelation;
use common\models\CardOrderItem;
use common\models\CardOrderPayback;
use common\models\CardRefundLog;
use common\models\InsuranceCoverage;
use maintainer\components\LoginedController;
use common\models\CardRefund;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

class RefundController extends LoginedController
{
    public function actionIndex()
    {
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $pageSize = Yii::$app->request->post('length', 10);
            $start = Yii::$app->request->post('start', 0);//偏移量
            $model = $this->_condition();
            $count = $model->count('*');
            $dataProvider = new ActiveDataProvider([
                'query' => $model->orderBy('status ASC')->limit($pageSize)->offset($start),
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

            foreach ($result as $k => $val) {
                $data['data'][$k] = [
                    $val->id,
                    $val->pay_sn,
                    $val->coverage_code,
                    $val->number,
                ];
                if (!$seller_info->pid) {
                    $data['data'][$k][] = '<span class="font-purple-seance">¥ ' . $val->total_price . '</span>';
                }
                $data['data'][$k][] = '<span class="font-purple-seance">' . CardRefund::refundStateData()[$val->status] . '</span>';
                $data['data'][$k][] = date('Y-m-d H:i:s', $val->add_time);
//                $btn = '<button type="button" class="btn btn-small btn-outline blue " onclick="showInfo(' . $val->id . ')" >查看退卡详情</button>';
                $btn = '<a href="'.$this->createUrl(['refund/view','id'=>$val->id]).'" class="btn btn-small btn-outline blue " >查看退卡详情</a>';
                if (!$val->status) {
                    $btn .= '<button type="button" class="btn btn-small btn-outline red " onclick="cancel(' . $val->id . ')" >取消退卡</button>';
                }
                $data['data'][$k][] = $btn;
            }
            echo json_encode($data);
            exit;
        }
        $this->render('index', ['seller_info' => $seller_info]);
    }


    /**
     * 退款申请
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionApplf()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $seller_info = Yii::$app->user->identity->getSellerInfo();
            $seller_id = $seller_info->seller_id;
            $cards = Yii::$app->request->post('card_number_str', '');
            $note = Yii::$app->request->post('card_remark', '');
            $order_id = Yii::$app->request->post('order_id','');
            if (!$cards || !$order_id) return $this->getCheckNo('参数错误');
            $model_item = CardOrderItem::findOne(['order_id'=>$order_id]);
            if(!$model_item){
                return $this->getCheckNo('当前订单不存在');
            }
            $transaction = Yii::$app->getDb()->beginTransaction();
            $data = [];
            try {
                $cards = helper::creadCard($cards);
                if (!$cards) {
                    throw new Exception('卡券号输入错误');
                }
                $check=CardGrantRelation::checkOrderCard($cards,$order_id);
                if($check){
                    throw new Exception('卡券（'.join(',',$check).'）不属于当前发放订单，请确认后再次退卡');
                }
                //如果申请的卡券中有险种信息，则判断当前卡券是否全部属于当前险种，不然则报错
                $coverage_code = $model_item->coverage_code;
                if($coverage_code){
                    $result=CardCouponsGrant::find()->where(['card_number'=>$cards])->asArray()->all();
                    $result_code = array_unique(array_column($result,'coverage_code'));
                    if(count($result_code) > 1 ){
                        $result_number = array_column($result,'card_number');
                        $diff_number = array_diff($cards,$result_number);
                        throw new Exception('卡券号 '.join(',',$diff_number).' 不属于 '.$coverage_code .' 险种');
                    }
                }
                //一级商家则计算退款金额，否则则不计算
                if ($seller_info->pid) {
                    $to_seller_id = $pid = $seller_info->pid;
                    $total = 0;
                } else {
                    $pid = $seller_id;
                    $to_seller_id = 1;
                }
                //检测当前卡券是否属于当前商家
                if (!CardGrantRelation::checkIssue($cards, $seller_id)) {
                    throw new Exception('当前卡券中包含不属于您的卡券');
                }
                //检查卡券是否可退
                $stat = CardCouponsGrant::checkRefund($pid, $cards);
                if (!$stat['status']) {
                    $data = [
                        'code' => '1000',
                        'data' => $stat['data']
                    ];
                    throw new Exception($stat['msg']);
                }
                $coverage = $stat['data']['coverage'];
                //检查卡券是否包含正在退款的卡券
                if ($refund = CardRefund::checkRe($cards, $seller_id)) {
                    $refund_card = $refund;
                    $data = [
                        'code' => '1001',
                        'data' => array_diff($cards, $refund_card)
                    ];
                    throw new Exception('卡券 [ ' . join(',',array_intersect($cards,$refund_card)) . ' ] 正在退卡或已经退卡，请检查后重新退款');
                }
                if (!$seller_info->pid) {
                    //统计卡券价格
                    $sum = CardCouponsGrant::getCountPrice($cards);
                }
                $model = new CardRefund();
                $model->add_time = time();
                $model->card_numbers = join(',', $cards);
                $model->from_seller_id = $seller_id;
                $model->to_seller_id = $to_seller_id;
                $model->total_price = $seller_info->pid ? $total : $sum;
                $model->number = count($cards);
                $model->coverage_code = $coverage_code;
                $model->pay_sn =$model_item ->pay_sn;
                $ret = $model->save();
                if ($ret) {
                    $cards =explode(',',$model->card_numbers);
                    $cards = array_map(function($card){
                        return "'".$card."'";
                    },$cards);

                    //冻结卡券
                    $sql2 = 'update '.CardCouponsGrant::tableName().' set status = '.CardCouponsGrant::__STATUS_FROZE .' where card_number in('.join(',',$cards).')';
                    $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
                    $member = Yii::$app->user->identity;
                    $model_log = new CardRefundLog();
                    $model_log->refund_id = $model->id;
                    $model_log->uid = $member->id;
                    $model_log->name = $seller_info->seller_name;
                    $model_log->update_time = date('Y-m-d H:i:s');
                    $model_log->content = $note ? $note : $seller_info->seller_name . '于' . $model_log->update_time . ' 发起退卡 ' . $model->number . '张';
                    if ($model_log->save() && $ret2) {
                        $transaction->commit();
                        return $this->getCheckYes([], '退卡申请成功');
                    }
                }
                throw new Exception('退卡申请失败。');
            } catch (Exception $e) {
                $transaction->rollBack();
                return $this->getCheckNo($e->getMessage(), $data);
            }
        }
        Yii::$app->params['_menu'] = '_index';
        $order_id = Yii::$app->request->get('order_id','');
        if($order_id)
            $model=CardOrderItem::findOne(['order_id'=>$order_id]);
        $this->render('applf',['order_id'=>$order_id,'model'=>$model]);
    }


    /**
     * 取消退卡
     * @return array
     */
    public function actionCancel()
    {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            $this->showMessage('非法操作');
        }
        $id = Yii::$app->request->post('id', '');
        if (!$id) return $this->getCheckNo('参数错误');
        $model = CardRefund::findOne(['id' => $id]);
        if (!$model && !$model['status']) {
            return $this->getCheckNo('当前退卡记录已进入退卡流程，不能取消。');
        }
        $model->status = 3;
        if ($model->save()) {
            $model_log = new CardRefundLog();
            $model_log->uid = Yii::$app->user->identity->id;
            $model_log->name = Yii::$app->user->identity->getSellerInfo()->seller_name;
            $model_log->update_time = date('Y-m-d H:i:s');
            $model_log->content = '商家 ' . $model_log->name . ' 取消了退卡申请。';
            $model_log->refund_id = $id;
            $model_log->save();
            return $this->getCheckYes([], '取消退卡成功');
        }
        return $this->getCheckNo('取消退卡失败');
    }

    /**
     * 退卡详情
     * @return array
     */
    public function actionInfo()
    {
        if (!Yii::$app->request->isAjax) {
            $this->showMessage('非法访问');
        }
        $id = Yii::$app->request->get('id', '');
        if (!$id) return $this->getCheckNo('参数错误');
        $model = CardRefund::find()->where(['id' => $id])->asArray()->one();
        if ($model) {

            $log = CardRefundLog::find()->where(['refund_id' => $id])->orderBy('id desc')->asArray()->all();
            $seller_info = Yii::$app->user->identity->getSellerInfo();
            $seller_id = $seller_info->pid;
            $model['add_time'] = date('Y-m-d H:i:s', $model['add_time']);
            return $this->getCheckYes(['info' => $model, 'log' => $log, 'pid' => $seller_id], '');
        }
        return $this->getCheckNo('详情获取失败');
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $pay_info = CardOrderPayback::findOne(['pay_sn'=>$model->pay_sn]);
        Yii::$app->params['_menu']='_index';
        return $this->render('refund_view', ['model' => $model,'pay_info'=>$pay_info]);
    }

    private function _condition($tj = [])
    {
        $seller_info = Yii::$app->user->identity->getSellerInfo();
        $seller_id = $seller_info->seller_id;
        $model = CardRefund::find();
        $where = [
            'from_seller_id' => $seller_id
        ];
        $post = Yii::$app->request->post();
        if (isset($post['status']) && $post['status'] !== '') {
            $where['status'] = (int)$post['status'];
        }

        $condition = array_merge($where, $tj);
        $model->where($condition);
        if (isset($post['keyword']) && $post['keyword']) {
            $model->andWhere(" POSITION( {$post['keyword']} IN `card_numbers`) ");
        }
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