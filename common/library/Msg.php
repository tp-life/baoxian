<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 10:46
 */

namespace common\library;


class Msg
{

    /**
     * 提现申请
     * @return string
     */
    public static function withdrawalTemp(){
        $str=<<<WOK
    商家[ {seller_name} ]于{time},申请提现 {price}元.
WOK;
        return $str;
    }

    /**
     * 打款申请
     * @return string
     */
    public static function paymentTemp(){
        $str=<<<WOK
    您的维保订单#{m_order_id}于{time}收到提现金额 {price}元.
WOK;
        return $str;
    }

    /**
     * 维修指派
     * @return string
     */
    public static function assignedTemp(){
        $str=<<<WOK
    您于{time}收到订单号为{order_sn},手机型号:{brand_model}的维修指派.请尽快安排维修!
WOK;
        return $str;
    }

    //发送验证码
    public static  function sendTemp(){
        return "您于{time}手机验证码：{code}。请勿将验证码泄露给他人。";
    }

    //订单提交资料成功
    public static function orderTemp(){
        $tel = \Yii::$app->params['concat'];
        return "您的资料已提交成功，我们会在24小时内进行审核，审核通过第8天0点生效，如有任何疑问，请联系客服热线：{$tel}。";
    }

    //订单审核成功
    public  static  function  applfSuccessTemp(){
        $tel = \Yii::$app->params['concat'];
        return "尊敬的用户，您的订单（{order_sn}）已审核成功，服务生效日期{start}，截止日期{end}，如有任何疑问，请拨打{$tel}，感谢您对乐换新的关注！ ";
    }

    //订单照片审核失败
    public static  function phoneErrTemp(){
        $tel = \Yii::$app->params['concat'];
        $wx = \Yii::$app->params['wxaccount'];
        return "尊敬的用户，您的订单因为照片问题不符合要求，尚未审核通过，请通过“{$wx}”微信公众号——我的订单—完善资料、重新提交照片审核。客服热线：{$tel}";
    }

    //订单手机IMEI号审核失败
    public static  function  imeiErrTemp(){
        $tel = \Yii::$app->params['concat'];
        $wx = \Yii::$app->params['wxaccount'];
        return "尊敬的用户，您的订单因为手机IMEI号码填写错误，尚未审核通过，请通过“{$wx}”微信公众号——我的订单—完善资料、重新提交IMEI号审核。客服热线：{$tel}";
    }

    //品牌信号错误审核失败
    public static  function brandErrTemp(){
        $tel = \Yii::$app->params['concat'];
        $wx = \Yii::$app->params['wxaccount'];
        return "尊敬的用户，您的订单因为品牌型号填写错误，尚未审核通过，请通过“{$wx}”微信公众号——我的订单—完善资料、重新提交品牌型号审核。客服热线：{$tel}";
    }

    //服务生效短信
    public static  function  securityTemp(){
        $tel = \Yii::$app->params['concat'];
        return "尊敬的用户，您的碎屏维修服务已生效，订单号为{order_sn}，服务生效日期{start}，截止日期{end}，如有任何疑问，请拨打{$tel}，感谢您对乐换新的关注！";
    }
}