<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/11
 * Time: 14:44
 */

namespace common\tool;

use common\library\helper;
use common\models\SmsLog;
use common\models\SmsQueue;
use yii\base\Object;

class Sms extends Object
{

	const SMS_PREFIX = '【欢欣科技】';//前缀
	const SMS_SUFFIX = '';//后缀  作为网站短信签名 不能乱改欢欣网
	const SMS_SEND_URL = 'http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/sendsms.action';
	const SMS_REGISTER_URL = 'http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/regist.action';
	const SMS_NAME = '9SDK-EMY-0999-JFSUP';
	const SMS_PWD = '223226';
	const NEXT_TIME = 60; //下一次发送时间

	const TYPE_ORDER = "ORDER";//订单
	const TYPE_USER_VERFIY_CODE = "USER_VERFIY_CODE";//验证码
	const TYPE_ADMIN_MSG = "TYPE_ADMIN_MSG";//验证码

	const TEST_DEV_SMS_TIE = '##Test##';

	static $send_limit = 20;//每天 单个手机号最多20条短信
	static $send_limit_type = array('USER_VERFIY_CODE', 'ORDER');


	static function getTypeData($key_code = '')
	{
		$d = [
			self::TYPE_USER_VERFIY_CODE => '验证码',
			self::TYPE_ORDER => '用户订单',
			self::TYPE_ADMIN_MSG => '人工发送'
		];

		return isset($d[$key_code]) ? $d[$key_code] : $d;
	}


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
		//短信验证码类型相关短信 发送限制处理
		if (in_array($type, self::$send_limit_type)) {
			if (SmsLog::gainNumberCode($mobile, $type) >= self::$send_limit) {
				return false;
			}
		} else {
			return false;
		}

		//发送时间处理
		if(!self::checkSendTime($mobile,$type)){
			return false;
		}
		//格式化内容
		$content = self::smsFormat($message);

		$params['type'] = $type;
		$params['phone'] = $mobile;
		$params['content'] = $content;
		$params['ip'] = Fun::getClientIp();
		$params['agent'] = $_SERVER['HTTP_USER_AGENT'];
		$params['send_time'] = date("Y-m-d H:i:s", time());

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
			$param['message'] = $content;
			$param['seqid'] = '';
			$param['addserial'] = '';

			$res = self::getCurl(self::SMS_SEND_URL, $param, 'get');
			preg_match('/<error>(.*)<\/error>/isU', $res, $m);

			if ($m && isset($m[1]) && $m[1] == 0) {
				//发送成功
				self::addHistoryLog($params);
				return true;
			}
			//发送失败 加入队列
			return self::addLog($params);
		}
		//加入发送队列
		return self::addLog($params);
	}


	protected static function  smsFormat($message = '')
	{
		if (YII_DEBUG) {
			//本地测试短信标识
			return self::SMS_PREFIX . $message . self::TEST_DEV_SMS_TIE . self::SMS_SUFFIX;
		}
		return self::SMS_PREFIX . $message . self::SMS_SUFFIX;

	}

	public static function checkSendTime($phone, $type)
	{
		$time =  time();
		$last_time = SmsLog::getLastSendTime($phone, $type);
		return (strtotime($last_time) + self::NEXT_TIME) < $time;
	}

	/**
	 * 加入短信队列
	 */
	public static function addLog($arr = array())
	{

        $model=SmsQueue::findOne(['type'=>'USER_VERFIY_CODE','phone'=>$arr['phone']]);
		$obj =$model?$model: new SmsQueue();
		$obj->setAttributes($arr);
		if ($obj->save()) {
			return $obj;
		}
		return false;
	}

	/**
	 * 加入短信历史
	 */
	public static function addHistoryLog($arr = array())
	{
		$obj = new SmsLog();
		$obj->setAttributes($arr);
		$obj->insert();
		return $obj;
	}


	/**
	 * 6验证码 生成规则
	 * @param $phone 11 位 有效手机
	 */
	public static function gainCode($phone='')
	{
		if (YII_DEBUG) {
			return 666666;
			//本地测试短信标识
		}

		return rand(100000, 999999);
	}

	public static function getVerifyMessage($code)
	{
		$time = date('Y-m-d');
		return "您于{$time}手机验证码：{$code}。请勿将验证码泄露给他人。";
	}

    /**
     * 验证码检测
     * @param string $phone
     * @param string $captcha
     * @param string $code_type
     * @return bool
     */
	public static function checkVerifyCode($phone='',$captcha='',$code_type=self::TYPE_USER_VERFIY_CODE){
        $log = SmsLog::findOne(['captcha'=>$captcha,'type'=>$code_type,'phone'=>$phone]);
        if($log){
            return true;
        }
        return false;

    }

}