<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 16/8/9
 * Time: am 11:30
 * Note:所有后台需求登录访问的请求须继承此控制
 */

namespace maintainer\components;
use common\models\Seller;
use Yii;
use yii\helpers\Json;

class LoginedController extends BaseController
{

	protected $user = null;
	protected $seller = null;

	public function beforeAction($action)
	{
		if ($this->enableCsrfValidation && Yii::$app->getErrorHandler()->exception === null && !Yii::$app->getRequest()->validateCsrfToken()) {
			if (Yii::$app->request->isAjax) {
				die(Json::encode($this->getCheckNo('Token 验证失败')));
			} else {
				$this->showMessage('Token 验证失败，请联系管理员', '异常提示', self::__MSG_DANGER);
			}
		}
		if (parent::beforeAction($action)) {
			if (Yii::$app->user->isGuest) {
				$this->redirect(['site/login']);
				return false;
			}
			$this->user = Yii::$app->user->identity;
			$this->seller = Yii::$app->user->identity->sellerInfo;
			if(in_array($this->seller->seller_id,Seller::$lehuanxin) ){
				Yii::$app->user->logout(true);
				$this->redirect(['site/login']);
				return false;
			}
		}
		return true;
	}
}