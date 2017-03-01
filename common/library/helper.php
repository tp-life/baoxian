<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/8/17
 * Time: 上午11:05
 */

namespace common\library;


use common\models\Area;
use common\models\BrandModel;
use common\tool\Sms;

class helper
{
    /**
     * 获取地区下拉框数据
     * @param int $pid
     * @param int $id
     * @return string
     */
    public static function getAreaSelect($pid=0,$id=0){
        $area=Area::findAll(['area_parent_id'=>$pid]);
        $area_html='';
        foreach($area as $val){
            $s=$val->area_id == $id?'selected':'';
            $area_html.='<option value="'.$val->area_id.','.$val->area_name.'" '.$s.'>'.$val->area_name.'</option>';
        }
        return $area_html;
    }

    /**
     * 取得订单支付类型文字输出形式
     *
     * @param array $payment_code
     * @return string
     */
    public static function orderPaymentName($payment_code) {
        return str_replace(
            array('offline','online','alipay','tenpay','chinabank','predeposit','wxpay','wx_jsapi','wx_saoma','chain','unionpay','kaquan'),
            array('线下支付','在线付款','支付宝','财付通','网银在线','站内余额支付','微信支付[客户端]','微信支付[jsapi]','微信支付[扫码]','门店支付','银联支付','卡券激活'),
            $payment_code);
    }

    /**
     * 获取品牌选择下拉框
     * @param int $pid 上级ID
     * @param int $id 当前ID
     * @return string
     */
    public static  function getBrandModel($pid=0,$id=0){
        $brand=BrandModel::findAll(['parent_id'=>$pid]);
        $brand_html='';
        foreach($brand as $val){
            $s=$val->id == $id?'selected':'';
            $brand_html.='<option value="'.$val->id.','.$val->model_name.'" '.$s.'>'.$val->model_name.'</option>';
        }
        return $brand_html;
    }

    /**
     * 替换消息模板中数据函数
     * @param null $type  消息类型
     * @param array $data 模板消息数据
     * @return bool|mixed
     */
    public static function handleMsg($type=null,$data=[]){
        if(!$type || !is_string($type)){
            return '';
        }
        $data['time']=isset($data['time'])?$data['time']:date('Y-m-d H:i');
        $type=rtrim($type,'Temp').'Temp';
        if(!method_exists('\common\library\Msg',$type)){
            return '';
        }
        $temp=call_user_func('\common\library\Msg::'.$type);
        foreach($data as $k=>$v){
            $temp=str_replace('{'.$k.'}',' '.$v.' ',$temp);
        }
        return $temp;
    }


    /**
     * 发送指定模板的内容
     * @param null $type
     * @param array $data
     * @return bool|\common\models\SmsQueue
     */
    public static function sendSms($type=null,$data=[],$model=''){
        if(!$type || !is_string($type)){
            return false;
        }
        if(!isset($data['tel']) || empty($data['tel']) || !is_numeric(trim($data['tel']))){
            return false;
        }
        $msg = self::handleMsg($type,$data);
        return Sms::sendSMS(trim($data['tel']),$msg,$model);
    }


    /**
     * 创建订单号
     * @param $member_id 用户ID
     * @return string
     */
    public static function  _makeOrderSn($member_id=0) {
    return mt_rand(10,99)
    . sprintf('%010d',time() - 946656000)
    . sprintf('%03d', (float) microtime() * 1000)
    . sprintf('%03d', (int) $member_id % 1000);
}





    /**
     * 生成发放的卡券
     * @param string $codes
     * @return array
     */
    public static function creadCard($codes=''){
        if(!$codes) return [];
        $codes=trim($codes,',，');
        $codes = str_replace('，',',',$codes);
        $cards = explode(',',$codes);
        $arr=[];

        foreach($cards as $val){
            $val=trim($val);
            if(!$val) continue;
//            if(!self::checkNumber($val)) continue;
            if(strpos($val,'|') !==false){
                list( $s,$e)=explode('|',$val);
                $rand =range(1 .$s,1 . $e);
                foreach($rand as $v){
                    $arr[]=substr((string)$v,1);
                }
            }else{
                $arr[]=$val;
            }
        }
        return $arr;
    }

    /**
     * 检测看全格式
     * @param string $card
     * @return int
     */
    public static  function checkNumber($card=''){
        $card=trim($card);
        $reg = '/^\d{7,}$/';
        $reg2 ='/^\d{7,}\|\d{7,}?/';
        return preg_match($reg,$card) || preg_match($reg2,$card);
    }

    /**
     * 日志
     * @param string $name
     * @param string $log
     */
    public static function log($log='',$name='test.log'){
        $dir = \Yii::getAlias('@runtime').'/logs/';
        $file_name=$dir.date('Y-m-d').'_'.$name;
        file_put_contents($file_name,$log."\t\n",FILE_APPEND);
    }
}

