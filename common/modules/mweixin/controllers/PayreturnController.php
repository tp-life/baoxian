<?php

namespace common\modules\mweixin\controllers;

use common\library\helper;
use common\models\Order;
use common\models\OrderLog;
use common\models\PaymentLog;
use common\wxpay\Wpay;
use common\wxpay\WxHelp;
use Yii;

/**
 * api for 支付回调
*/

class PayreturnController extends \yii\web\Controller
{
    static $handle;
    public function __construct($id, \yii\base\Module $module, array $config=[])
    {
        parent::__construct($id, $module, $config);

    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
		$this->fileLog();
        $msg='OK';
        static::$handle = WxHelp::WxPayNotifyReply();
        $handle = static::$handle;
        $result= Wpay::notify(array($this,'handlePay'),$msg);
        if($result == false || !$result['state'] ){
            $msg = isset($result)?$result['msg']:'请求错误';
            $handle->SetReturn_code("FAIL");
            $handle->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return;
        } else {
            $handle->SetReturn_code("SUCCESS");
            $handle->SetReturn_msg("OK");
        }
        $this->ReplyNotify(true);
    }

    /**
     * 支付测试回调
     */
    public function actionTest(){
		$this->fileLog();
        $msg='OK';
        static::$handle = WxHelp::WxPayNotifyReply();
        $handle = static::$handle;
        $result= Wpay::notify(array($this,'handlePay'),$msg);
        if($result == false || !$result['state'] ){
            $msg = isset($result)?$result['msg']:'请求错误';
            $handle->SetReturn_code("FAIL");
            $handle->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return;
        } else {
            $handle->SetReturn_code("SUCCESS");
            $handle->SetReturn_msg("OK");
        }
        $this->ReplyNotify(true);
    }

    public function handlePay($data=[]){
        $return= ['state'=>false,'msg'=>''];
        if($data['return_code']=='SUCCESS' && $data['result_code']=='SUCCESS'){
            if($data['appid'] == WxHelp::getWxConfig('APPID') && $data['mch_id']  == WxHelp::getWxConfig('MCHID')){
                $order_sn = $data['out_trade_no'];
                $pay_sn = $data['transaction_id'];
                $order_info=Order::findOne(['order_sn'=>$order_sn]);
                if(!$order_info){
                    $return['msg']='商户订单不存在';
                    goto end;
                }
                if($order_info ->order_state >= Order::__ORDER_PAY){
                    $return['state']=true;
                    goto end;
                }
                $state = $order_info->order_state;
                $order_info ->order_state = Order::__ORDER_PAY;
                $order_info->pay_sn = $pay_sn;
                $order_info->payment_time = time();
                $order_info->payment_code ='wxpay';
                $log="支付回调开始（{$order_sn}）:".time()."\t\n";
                $log.=var_export($data,true);
                $log.="支付回调结束".time()."______________\t\n";
                @helper::log($log,'pay_return.log');
                if($order_info->save()){
                    $log=['order_id'=>$order_info->order_id,'before_order_state'=>$state,'order_state'=>$order_info->order_state,'log_msg'=>'用户微信支付'.$data['total_fee'].'(分)成功','log_time'=>date('Y-m-d H:i:s')];
                    $model = new OrderLog();
                    $model->insertLog($log);
                    $payment = new PaymentLog();
                    $payment_log = ['order_id'=>$order_info->order_id,'pay_sn'=>$pay_sn,'data'=>serialize($data),'end_time'=>time(),'remark'=>'用户（'.$order_info->member_name.'）完成支付','add_time'=>time()];
                    $payment->insertLog($payment_log);
                    $return['state']=true;
                }
            }
        }
        end:{
            return $return;
        }
    }

    private function ReplyNotify($needSign = true)
    {
        $handle = static::$handle;
        //如果需要签名
        if($needSign == true && $handle->GetReturn_code() == "SUCCESS")
        {
            $handle->SetSign();
        }
        Wpay::replyNotify($handle->ToXml());
    }
	private function fileLog()
	{
		$action_id = Yii::$app->controller->action->id;
		$data = file_get_contents("php://input");
		file_put_contents(Yii::getAlias('@runtime').'/logs/pay_'.date("Y-m-d").'_'.$action_id.'.log',var_export($data,true),FILE_APPEND);
	}
}
