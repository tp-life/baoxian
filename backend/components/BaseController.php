<?php
/**
 * Created by PhpStorm.
 * User: tp
 * Date: 16/8/8
 * Time: 下午3:12
 * Note:后台管理基础控制器 所有必须继承此控制
 */

namespace backend\components;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends  Controller
{

	public $title;
	public $description;
	public $author;


    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }
	public function __construct($id, $module, $config = [])
	{
		$this->title = '乐换新&保险管理系统';
		$this->description = '乐换新 保险管理系统';
		$this->author = 'Tang&Leo.yan .Th';
		$this->id = $id;
		$this->module = $module;
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

	public $responData = ['code' => 'no', 'message' => '非法请求', 'data' => []];

	/**
	 * Success处理
	 */
	public function getCheckYes($data = array(),$message = 'Success')
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$this->responData['code'] = 'yes';
		$this->responData['message'] = $message;
		$this->responData['data'] = $data;
		return $this->responData;
	}
	/**
	 * Failure处理
	 */
	public function getCheckNo($message = 'Failure Respones',$data = array())
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$this->responData['code'] = 'no';
		$this->responData['message'] = $message;
		$this->responData['data'] = $data;
		return $this->responData;
	}

	/**
	 * 中间页面跳转处理 message 展示
	 * $message  string|array
	 * $type info|danger
	*/
	const __MSG_INFO = 'info';
	const __MSG_DANGER = 'danger';

	public function showMessage($message, $title = '操作提示', $type = 'info', $href = '', $wait = 3)
	{
		$data = array(
			'message' => $message,
			'title' => $title ? $title : '操作提示',
			'href' => $href,
			'wait' => $wait,
			'type' => $type
		);
		if (empty($href)) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$href = $_SERVER['HTTP_REFERER'];
				if (stripos($title, '异常') !== false) {
					$href = 'javascript:window.close();';
				} elseif (stripos($href, $_SERVER['REQUEST_URI']) !== false) {
					//$href = 'javascript:window.close();';
					$href = Url::to([Yii::$app->controller->id]);
				}
			}
			$data['href'] = $href;

		}
		$this->layout = null;
		echo $this->render("//layouts/messageBox", $data);
		exit;
	}

    /**
     * @param $model_error
     * @return string
     * 用来解析 模型的错误
     */
    public function getModelErrorsStr($model_error) {
        $arr = [];
        foreach ($model_error as $error) {
            $arr[] = implode(PHP_EOL, $error);
        }
        return implode(PHP_EOL, $arr);
    }

}