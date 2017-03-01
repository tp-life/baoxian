<?php

namespace backend\controllers;

use backend\components\LoginedController;
use common\models\AdminLog;
use common\models\Role;
use common\models\SmsLog;
use common\models\SmsQueue;
use common\tool\Sms;
use Yii;
use common\models\Admin;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * 短信日志smslog
 */
class SmsController extends LoginedController
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [];

	}

	public function actionCreate()
	{
		$model = new SmsQueue();
		$this->_dataAdapter($model);
		return $this->render('create', ['model' => $model]);
	}

	protected function _dataAdapter($model)
	{
		if (Yii::$app->request->isPost) {
			$phone = Yii::$app->request->post('phone', '');
			if (!preg_match('/^1[34578]{1}\d{9}$/', $phone)) {
				$this->showMessage('电话号码格式错误','',self::__MSG_DANGER,Url::to(['sms/create']));
			}
			$content = Yii::$app->request->post('content', '');
			if(empty($content) || mb_strlen($content)<20){
				$this->showMessage('短信内容至少在20个字符，否则不予发送','',self::__MSG_DANGER,Url::to(['sms/create']));
			}
			if (Sms::sendSMS($phone,$content)) {
				$this->showMessage('短信成功加入队列','',self::__MSG_INFO,Url::to(['sms/index']));
			}
			$this->showMessage('短信发送失败,请检查后重试','',self::__MSG_DANGER,Url::to(['sms/create']));
		}
	}

	protected function findModel($id)
	{
		if (($model = SmsLog::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionIndex()
	{
		if (Yii::$app->request->isAjax) {
			$respon = array();
			$pageSize = Yii::$app->request->post('length', 10);
			$start = Yii::$app->request->post('start', 0);//偏移量
			$phone = Yii::$app->request->post('phone', '');
			$type = Yii::$app->request->post('type', '');

			$conditon = [];
			if ($phone = trim($phone)) {
				$conditon['phone'] = $phone;
			}
			if($type){
				$conditon['type'] = $type;
			}
			$params = array();
			$total = SmsLog::find()->where($conditon, $params)->count('log_id');
			$dataProvider = new ActiveDataProvider([
				'query' => SmsLog::find()->where($conditon, $params)->orderBy('log_id DESC')->limit($pageSize)->offset($start),
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => intval($start / $pageSize),
					'totalCount' => $total
				],
			]);

			if ($data = $dataProvider->models) {
				foreach ($data as $item) {
					$respon[] = [
						Sms::getTypeData($item->type),
						$item->phone,
						'<span class="font-green">' . $item->content . '</span>',
						$item->send_time,
						''
					];
				}
			}
			return json_encode(array('data' => $respon, 'recordsTotal' => $total, 'recordsFiltered' => $total));
		}

		return $this->render('index');
	}

}
