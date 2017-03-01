<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 16/8/9
 * Time: am 11:30
 * Note:所有后台需求登录访问的请求须继承此控制
 */

namespace backend\components;

use common\models\RoleAccess;
use common\models\RoleAction;
use common\models\RoleModule;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;

class LoginedController extends BaseController
{
	protected  $time;

	public function beforeAction($action)
	{
		$this->time = time();
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
		}

		//rbac check
		$admin = Yii::$app->user->identity;
		$role_id = $admin->role_id;

		$controllerId = Yii::$app->controller->id;
		$actionId = Yii::$app->controller->action->id;
		$table_access = RoleAccess::tableName();
		$table_moudel = RoleModule::tableName();
		$table_action = RoleAction::tableName();

		$access_sql = <<<leo
							SELECT
								count(a.id)
							FROM
								{$table_access} AS a
							LEFT JOIN {$table_moudel} AS b ON a.module_id = b.id
							LEFT JOIN {$table_action} c ON a.action_id = c.id
							WHERE
								a.role_id = {$role_id}
							AND b.is_effect = 1
							AND b.module = '{$controllerId}'
							AND c.action = '{$actionId}'
leo;

		if (!YII_DEBUG) {
			//系统角色
			if($admin->getIsSystemRole()){
				return true;
			}
			$isAccess = Yii::$app->db->createCommand($access_sql)->queryScalar();
			if (!$isAccess) {
				if (Yii::$app->request->isAjax) {
					die(Json::encode($this->getCheckNo('权限不足，请联系管理员')));
				} else {
					$this->showMessage('权限不足，请联系管理员', '错误提示', self::__MSG_DANGER);
				}
				return false;
			}
		}

		return true;
	}
}