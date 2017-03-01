<?php

namespace common\modules\mweixin\controllers;
use common\models\InsuranceCoverage;
use common\models\Order;
use common\models\OrderLog;
use common\models\PaymentLog;
use common\models\WxMember;
use common\tool\Fun;
use common\wxpay\Wpay;
use common\wxpay\WxHelp;
use weixin\components\BaseController;
use Yii;
use yii\base\Exception;

/**
 * api for 支付
*/

class PayController extends BaseController
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
		$order_id = Yii::$app->request->post('order_id');
        if(!$order_id){
            return $this->getCheckNo('参数错误');
        }
        $order_info = Order::findOne(['order_id'=>$order_id]);
        if(!$order_info){
            return $this->getCheckNo('订单不存在！');
        }
        if($order_info['order_state'] != Order::__ORDER_DEFAULT){
            return $this->getCheckNo('订单状态不正确');
        }
//        if($order_info['member_id'] != $this->member_id){
//            return $this->getCheckNo('当前订单不属于当前用户');
//        }
//        $coverage_info=InsuranceCoverage::findOne(['id'=>$order_info->coverage_id]);
        $input= WxHelp::WxPayUnifiedOrder();
        $payment_info=PaymentLog::find()->where(['order_id'=>$order_id])->andWhere(['>','end_time',time()+180])->orderBy('id DESC')->one();
        if($payment_info && $payment_info ->pay_sn){
            $order = unserialize($payment_info->data);
            $returnData=[
                'prepay_id'=>$order['prepay_id'],
                'appid'=>$order['appid'],
                'openId'=>$payment_info->open_id,
                'sign'=>$order['sign'],
                'nonce_str'=>$order['nonce_str'],
                'mch_id'=>$order['mch_id']
            ];
            $jsapi = Wpay::GetJsApiParameters($order);
            return $this->getCheckYes(['pay_arg'=>$jsapi,'pay_data'=>$returnData]);
        }
        try{
            $member_info=WxMember::findOne(['token'=>$this->token]);
            $openId = $member_info ->openid;
            //$input->SetBody('[测试]乐换新-购买保险 '.$order_info->coverage_name.'('.$order_info->coverage_code.')');
			$input->SetBody('乐换新-购买保险 '.$order_info->coverage_name.'('.$order_info->coverage_code.')');
            $input->SetAttach($order_info->order_sn);
            $input->SetOut_trade_no($order_info->order_sn);
            $input->SetTotal_fee($order_info->order_amount * 100);
            //$input->SetTotal_fee(1);
            $input->SetTime_start(date("YmdHis",time()));
			//$input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetNotify_url(WxHelp::getWxConfig('CALLBACK_URL'));
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = Wpay::unifiedOrder($input);
            if($order['return_code'] == 'SUCCESS' && $order['result_code'] =='SUCCESS'){
                $payment = new PaymentLog();
                $payment_log = ['order_id'=>$order_id,'pay_sn'=>$order['prepay_id'],'data'=>serialize($order),'end_time'=>time()+7200,'remark'=>'用户（'.$this->member_name.'）发起预支付','add_time'=>time()];
                $payment->insertLog($payment_log);
                $returnData=[
                    'prepay_id'=>$order['prepay_id'],
                    'appid'=>$order['appid'],
                    'openId'=>$member_info->openid,
                    'sign'=>$order['sign'],
                    'nonce_str'=>$order['nonce_str'],
                    'mch_id'=>$order['mch_id']
                ];
                $jsapi = Wpay::GetJsApiParameters($order);
               return $this->getCheckYes(['pay_arg'=>$jsapi,'pay_data'=>$returnData]);
            }
            $msg =$order['return_code'] == 'SUCCESS'?$order['err_code_des']:$order['return_msg'];
            throw  new Exception($msg);
        }catch (Exception $e){
            return $this->getCheckNo($e->getMessage());
        }
    }


    /**
     * 查询订单
     * @return array
     */
    public function actionQueryorder(){
        $order_id = Yii::$app->request->post('order_id');
        if(!$order_id){
            return $this->getCheckNo('参数错误');
        }
        $order_info = Order::findOne(['order_id'=>$order_id]);
        if(!$order_info){
            return $this->getCheckNo('订单不存在！');
        }
        if($order_info['pay_sn']){
            return $this->getCheckYes();
        }
        $query=$this->orderQuery($order_info->order_sn);
        if($query['state']){
            $order_info->pay_sn = $query['pay_sn'];
            $order_info->save();
            return $this->getCheckYes();
        }
        return $this->getCheckNo($query['msg']);
    }

    /**
     * 查询订单
     * @param string $order_pay
     * @param string $transaction_id
     * @return bool
     */
    private function orderQuery($order_pay='',$transaction_id=''){
        if(!$transaction_id && !$order_pay){
            return false;
        }
        $input = WxHelp::WxPayOrderQuery();
        if($transaction_id){
            $input->SetTransaction_id($transaction_id);
        }else{
            $input ->SetOut_trade_no($order_pay);
        }
        $result = Wpay::orderQuery($input);
        if($result['return_code'] =='SUCCESS' && $result['result_code'] =='SUCCESS'){
            if($result['trade_state'] == 'SUCCESS' ){
                return ['state'=>true,'pay_sn'=>$result['transaction_id']];
            };
            return ['state'=>false,'msg'=>$result['trade_state_desc']];
        }
        $r = ['state'=>false];
        $r['msg']=$result['return_code'] =='SUCCESS'?$result['err_code_des']:$result['return_msg'];
        return $r;
    }
}
