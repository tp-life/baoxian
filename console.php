<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 16:32
 */

class Console{
    const SMS_PREFIX = '【乐换新】';//前缀
    const SMS_SUFFIX = '';//后缀  作为网站短信签名 不能乱改欢欣网
    const SMS_SEND_URL = 'http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/sendsms.action';
    const SMS_REGISTER_URL = 'http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/regist.action';
    const SMS_NAME = '9SDK-EMY-0999-JFSUP';
    const SMS_PWD = '223226';
    const NEXT_TIME = 60; //下一次发送时间

    const TYPE_ORDER = "ORDER";//订单
    const TYPE_USER_VERFIY_CODE = "USER_VERFIY_CODE";//验证码

    const TEST_DEV_SMS_TIE = '##Test##';

    static $send_limit = 20;//每天 单个手机号最多20条短信
    static $send_limit_type = array('USER_VERFIY_CODE', 'ORDER');


    /**
     * 发送一个http请求
     * @param  $url    请求链接
     * @param  $method 请求方式
     * @param array $vars 请求参数
     * @param  $time_out  请求过期时间
     * @return JsonObj
     */
    static function getCurl($url, array $vars = array(), $method = 'post')
    {
        $method = strtolower($method);
        if ($method == 'get' && !empty($vars)) {
            if (strpos($url, '?') === false)
                $url = $url . '?' . http_build_query($vars);
            else
                $url = $url . '&' . http_build_query($vars);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        }
        $result = curl_exec($ch);
        if (!curl_errno($ch)) {
            $result = trim($result);
        } else {
            $result = '';
        }

        curl_close($ch);
        return $result;

    }

    /**
     * @param $mobile string 11
     * @param $message string to send content
     * @param $type self::$send_limit_type
     * @param $params other insert data eg ['captcha'=>'258SRT']
     * @param $is_send_in_time true or false, default false
     */
    static function sendSMS($mobile = '', $message = '', $type = 'USER_VERFIY_CODE', $params = array(), $is_send_in_time = false)
    {
        if (!preg_match('/^1[34578]{1}\d{9}$/', $mobile)) {
            return false;
        }
        //立即发送
        if ($is_send_in_time) {
            $param = array();
            $param['cdkey'] = self::SMS_NAME;
            $param['password'] = self::SMS_PWD;

            //第一次使用要注册
            if (isset($_SESSION['is_s_sms']) && $_SESSION['is_s_sms']) {
                $rg = self::getCurl(self::SMS_REGISTER_URL, $param, 'get');
                preg_match('/<error>(.*)<\/error>/isU', $rg, $mrg);

                if ($mrg && isset($mrg[1]) && $mrg[1] == 0) {
                    $_SESSION['is_s_sms'] = 1;
                }
            }
            $param['phone'] = $mobile;
            $param['message'] = $message;
            $param['seqid'] = '';
            $param['addserial'] = '';

            $res = self::getCurl(self::SMS_SEND_URL, $param, 'get');
            preg_match('/<error>(.*)<\/error>/isU', $res, $m);
            if ($m && isset($m[1]) && $m[1] == 0) {
                return true;
            }
        }
        return false;
    }

    protected static function  smsFormat($message = '')
    {
        return self::SMS_PREFIX . $message . self::SMS_SUFFIX;

    }
}