<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/25
 * Time: 11:13
 */

namespace common\wxpay;
use yii\base\Exception;

require_once "lib/WxPay.Api.php";

class Wpay
{
    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        if(method_exists('\WxPayApi',$name)){
            return call_user_func_array('\WxPayApi::'.$name,$arguments);
//            return \WxPayApi::$name(...$arguments);
        }
        return false;
    }

    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public static function GetJsApiParameters($UnifiedOrderResult)
    {

        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new Exception("参数错误");
        }
        $jsapi = WxHelp::WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(self::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = $jsapi->GetValues();
        return $parameters;
    }
}