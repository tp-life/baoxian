<?php
/**
 * Created by PhpStorm.
 * User: leo yan
 * Date: 16/11/10
 * Time: 下午3:12
 * Note:Api beseController
 */

namespace weixin\components;

use common\models\Member;
use common\models\WxMember;
use common\tool\Wechat;
use common\wxpay\WxHelp;
use maintainer\models\UploadForm;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class BaseController extends Controller
{


	public $member_id, $member_name, $member_phone, $token;
    const RETAILCLIENT = 'retail';

	public function beforeAction($action)
	{

		return parent::beforeAction($action);
	}

	public function afterAction($action, $result)
	{
		$result = parent::afterAction($action, $result);
		return $result;
	}

	public function __construct($id, $module, $config = [])
	{
        $this->weixinAuth();
		parent::__construct($id, $module, $config);
	}

	/**
	 * Creates a URL using the given route and query parameters.
	 *
	 * You may specify the route as a string, e.g., `site/index`. You may also use an array
	 * if you want to specify additional query parameters for the URL being created. The
	 * array format must be:
	 *
	 * ```php
	 * // generates: /index.php?r=site%2Findex&param1=value1&param2=value2
	 * ['site/index', 'param1' => 'value1', 'param2' => 'value2']
	 * ```
	 *
	 * If you want to create a URL with an anchor, you can use the array format with a `#` parameter.
	 * For example,
	 *
	 * ```php
	 * // generates: /index.php?r=site%2Findex&param1=value1#name
	 * ['site/index', 'param1' => 'value1', '#' => 'name']
	 * ```
	 *
	 * The URL created is a relative one. Use [[createAbsoluteUrl()]] to create an absolute URL.
	 *
	 * Note that unlike [[\yii\helpers\Url::toRoute()]], this method always treats the given route
	 * as an absolute route.
	 *
	 * @param string|array $params use a string to represent a route (e.g. `site/index`),
	 * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
	 * @return string the created URL
	 */
	public function createUrl($params)
	{
		return Yii::$app->urlManager->createUrl($params);
	}

	/**
	 * code 状态码
	 * 200 成功
	 * 400 失败
	 * 500 用户未登录
	 * @var array
	 */
	public $responData = ['code' => 400, 'message' => '非法请求', 'data' => []];

	/**
	 * Success处理
	 */
	public function getCheckYes($data = array(), $message = 'Success')
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$this->responData['code'] = 200;
		$this->responData['message'] = $message;
		$this->responData['data'] = $data;
		return $this->responData;
	}

	public function returnBack($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$this->responData = $data;
		return $this->responData;
	}

	/**
	 * Failure处理
	 */
	public function getCheckNo($message = 'Failure Respones', $data = array(), $code = 400)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$this->responData['code'] = $code;
		$this->responData['message'] = $message;
		$this->responData['data'] = $data;
		return $this->responData;
	}

	protected function checkToken()
	{
        $retail = trim(Yii::$app->request->cookies->getValue('retailClient'));
        if(trim($_REQUEST['retailClient']) === self::RETAILCLIENT || $retail === self::RETAILCLIENT){
            $is_login = trim(Yii::$app->request->cookies->getValue('isLogin'));
            $token =trim(Yii::$app->request->cookies->getValue('wxToken'));
            if($is_login == 1 && is_numeric($token)){
                $this->member_id = (int)$token;
                $this->token = $token;
            }
            return true;
        }
		$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
		//auto cookie
		$cookie_token = Yii::$app->request->cookies->get('wxToken');
		if (!$token) {
			if(!$cookie_token){
				$this->exitJson('缺少token!');
			}
			$token = $cookie_token;
		}
		$token = trim($token);
		$model = WxMember::findOne(['token' => $token]);
		if (!$model) {
			Yii::$app->response->cookies->remove(new Cookie(['name'=>'isLogin']));
			Yii::$app->response->cookies->remove(new Cookie(['name'=>'wxToken']));
			Yii::$app->response->cookies->remove(new Cookie(['name'=>'member_token']));
			$this->exitJson('token不正确,建议尝试清除微信存储缓存!');
		}
        $this->token = $token;
		if ($model->member_id) {
			$member_info = Member::findOne(['member_id' => $model->member_id]);
			if ($member_info) {
				$this->member_id = $model->member_id;
				$this->member_name = $member_info->name;
				$this->member_phone = $member_info->phone;
			}
		}
	}

	protected function exitJson($msg = '',$code=400,$data=[])
	{
		header("Content-type: application/json");
		echo \GuzzleHttp\json_encode($this->getCheckNo($msg,$data,$code));
		exit;
	}

	/**
	 * $_FILES 上传处理
	*/
	protected function updateFile($filename)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$root = Yii::getAlias('@webroot');
		$path = '/uploads/weixin_' . date('Ymd');
		if (!FileHelper::createDirectory($root . $path)) {
			return ['code' => 400, 'message' => '文件上传权限不足', 'data' => []];
		}
		$model = new UploadForm();
		$model->file = UploadedFile::getInstanceByName($filename);
		if ($model->file && $model->validate() ) {
			$baseName = $model->file->baseName;
			$extension = $model->file->extension;
			$file = strtolower($path . '/' . $filename . '_' . sha1($baseName . time()) . '.' . $extension);
			if ($model->file->saveAs($root . $file)) {
				//$file = '/'.ltrim($file, strtolower($root));
				return ['code' => 200, 'message' => 'Success', 'path' => $file];
			} else {
				$model->addError('file', '文件保存出错');
			}
		}
		return ['code' => 400, 'message' => '图片太大', 'data' => []];
		return ['code' => 400, 'message' => var_export($model->getErrors(), true), 'data' => []];
	}

	/**
	 * 二进制流文件上传处理
	*/
	protected function uploadBinaryFile()
	{

		$size = isset($_REQUEST['size'])?$_REQUEST['size']:0;
		$f_id = isset($_REQUEST['id'])?$_REQUEST['id']:'f';

		if(!$size){
			return ['code'=>400,'message'=>'上传文件错误','data'=>[]];
		}
		$root = Yii::getAlias('@webroot');
		$path = '/uploads/weixin_' . date('Ymd');
		if (!FileHelper::createDirectory($root . $path)) {
			return ['code' => 400, 'message' => '文件上传权限不足', 'data' => []];
		}

		$binarystring = file_get_contents("php://input","rb");
		if(!$binarystring){
			return ['code'=>400,'message'=>'文件流解析错误','data'=>[]];
		}
		$time = time();
		$filename = date('Ymd',$time).'_'.sha1($f_id.$time).'.jpg';
		$file = strtolower($path . '/' . $filename);
		$hand = fopen($root.$file,"wb");
		fwrite($hand,$binarystring);
		fclose($hand);
		return ['code'=>200,'message'=>'上传图片处理成功','data'=>['path'=>$file]];

	}

	/**
	 * 判断是否微信授权 以openid
	 */
	protected function weixinAuth()
	{
        $retailClient = trim(Yii::$app->request->get('retailClient',''));
		$cookie_token = Yii::$app->request->cookies->get('wxToken');
		$authInfo = Yii::$app->session->get('authInfo', null);
		$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$route = Yii::$app->requestedRoute;
		$isAjax = Yii::$app->request->isAjax;
		if ($isAjax || $retailClient === self::RETAILCLIENT) {
			 $this->checkToken();
		}else{
			//线上微信访问
			if(!$cookie_token){
				if (!$authInfo && $route !== 'site/auth' && (stripos($_SERVER['HTTP_HOST'], 'lehuanxin') !== false) && (strpos($userAgent, 'MicroMessenger') !== false)) {
					$appid = WxHelp::getWxConfig('APPID');
					$appse = WxHelp::getWxConfig('APPSECRET');
					$wechat = new Wechat(['appid' => $appid, 'appsecret' => $appse]);
					$authUrl = $wechat->getOauthRedirect(Yii::getAlias("@weixinSiteAuth"), 'baoxian'.$_SERVER['REQUEST_URI'],'snsapi_userinfo');
					//echo $authUrl;die;
					header("location:" . $authUrl);exit;
				}
			}

		}

	}


}