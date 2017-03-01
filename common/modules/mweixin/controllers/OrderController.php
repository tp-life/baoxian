<?php

namespace common\modules\mweixin\controllers;

use common\models\Order;
use common\models\OrderExtend;
use common\models\OrderLog;
use weixin\components\BaseController;
use Yii;

/**
 * api for 保险 完善资料
 */
class OrderController extends BaseController
{

    public function __construct($id, \yii\base\Module $module, array $config=[])
    {
        parent::__construct($id, $module, $config);
        if(!$this->member_id){
            $this->exitJson('请先登录后操作',500);
        }
    }

	/**
	 * 订单流程  完善资料  上传图片 见 buycontroller#actionPrefect
	 **/
	public function actionIndex()
	{
		$order_id = intval(Yii::$app->request->post('order_id', 0));
		$brand_id = intval(Yii::$app->request->post('brand', 0));
		$model_id = intval(Yii::$app->request->post('model', 0));
		$buyer_name = trim(Yii::$app->request->post('name', ''));
		$imei = trim(Yii::$app->request->post('imei', ''));
		$id_card = trim(Yii::$app->request->post('id_card', ''));
		//$is_confirm = Yii::$app->request->post('is_confirm', '');

		if (!$order_id || !$brand_id || !$model_id || !$buyer_name || !$imei || !$id_card) {
			return $this->getCheckNo('参数缺失');
		}
		if (!preg_match('/\w{10,20}/', $imei)) {
			return $this->getCheckNo('IMEI 格式错误');
		}
		if (!preg_match('/[\x{4e00}-\x{9fa5}A-Za-z]{2,10}/u', $buyer_name)) {
			return $this->getCheckNo('请输入机主姓名');
		}
		if (strlen($id_card) != 15 && strlen($id_card) != 18) {
			return $this->getCheckNo('您输入的机主有效身份证号码');
		}
		/*if (!$is_confirm) {
			return $this->getCheckNo('请确认已同意保险服务条款');
		}*/
		$order_status = [Order::__ORDER_PAY, Order::__ORDER_APPLF_ERR];
		$condition = ['member_id' => $this->member_id, 'order_state' => $order_status, 'order_id' => $order_id];
		$order = Order::findOne($condition);

		if (!$order) {
			return $this->getCheckNo('查无待完善资料保单记录');
		}
		if($order->order_state != Order::__ORDER_PAY &&  $order->order_state != Order::__ORDER_APPLF_ERR){
            return $this->getCheckNo('该订单资料已经完善');
        }
		$extends = OrderExtend::findOne(['order_id' => $order['order_id']]);
		if (!$extends) {
			return $this->getCheckNo('保单扩展信息有误');
		}
		$tran = Yii::$app->db->beginTransaction();
		try {
			$before_order_state = $order['order_state'];
			$update = [
				'buyer' => $buyer_name,
				'imei_code' => $imei,
				'idcrad' => $id_card,
				'brand_id' => $brand_id,
				'model_id' => $model_id,
				'is_data' => 1
			];

            $bstop=1;
            $flag1= true;
            if(in_array($extends->err_code,[Order::__ERR_IMEI,Order::__ERR_BRAND])){
                $bstop=0;
                $order->order_state = Order::__ORDER_APPLF;//提交资料 待审核
                $flag1 = $order->save();
            }
			//更新状态
//			$flag = OrderExtend::updateAll($update, ['common_id' => $extends['common_id']]) ;
            $extends->setAttributes($update);
            $flag = $extends->save();
			//记录日志
			$log = new OrderLog();
			$log->setAttributes([
				'order_id' => $order_id,
				'before_order_state' => $before_order_state,
				'order_state' => $order->order_state,
				'log_msg' => '更新完善保单资料',
				'log_user' => $order['member_name'],
				'log_time' => date('Y-m-d H:i:s')
			]);
			$log->save(false);
			if ($flag && $flag1) {
				$tran->commit();
				return $this->getCheckYes(['bstop'=>$bstop], '资料已提交，待审核');
			}
		} catch (Exception $e) {
			$tran->rollBack();
			return $this->getCheckNo($e->getMessage());
		}
		return $this->getCheckNo('完善资料失败,请重试');
	}

    /**
     * 购买后完善资料界面
     * @return array
     *
     */
	public function actionPefect(){
        $order_sn = trim(Yii::$app->request->post('order_sn', 0));
        $buyer_name = trim(Yii::$app->request->post('name', ''));
        $imei = trim(Yii::$app->request->post('imei', ''));
        $id_card = trim(Yii::$app->request->post('id_card', ''));
        //$is_confirm = Yii::$app->request->post('is_confirm', '');

        if (!$order_sn ||  !$buyer_name || !$imei || !$id_card) {
            return $this->getCheckNo('参数缺失');
        }
        if (!preg_match('/\w{10,20}/', $imei)) {
            return $this->getCheckNo('IMEI 格式错误');
        }
        if (!preg_match('/[\x{4e00}-\x{9fa5}A-Za-z]{2,10}/u', $buyer_name)) {
            return $this->getCheckNo('请输入机主姓名');
        }
        if (strlen($id_card) != 15 && strlen($id_card) != 18) {
            return $this->getCheckNo('您输入的机主有效身份证号码');
        }
        /*if (!$is_confirm) {
            return $this->getCheckNo('请确认已同意保险服务条款');
        }*/
        $order_status = [Order::__ORDER_PAY, Order::__ORDER_APPLF_ERR];
        $condition = ['order_state' => $order_status, 'order_sn' => $order_sn];
        $order = Order::findOne($condition);
        if (!$order) {
            return $this->getCheckNo('查无待完善资料保单记录');
        }
        if($order->order_state != Order::__ORDER_PAY &&  $order->order_state != Order::__ORDER_APPLF_ERR){
            return $this->getCheckNo('该订单资料已经完善');
        }
        $extends = OrderExtend::findOne(['order_id' => $order['order_id']]);
        if (!$extends) {
            return $this->getCheckNo('保单扩展信息有误');
        }
        $tran = Yii::$app->db->beginTransaction();
        try {
            $before_order_state = $order['order_state'];
            $update = [
                'buyer' => $buyer_name,
                'imei_code' => $imei,
                'idcrad' => $id_card,
                'is_data' => 1
            ];
            //更新数据
//            $order->order_state = Order::__ORDER_APPLF;//提交资料 待审核
//            $flag = $order->update(false,['order_state']);
            //更新状态
//            $flag = OrderExtend::updateAll($update, ['common_id' => $extends['common_id']]);
            $extends->setAttributes($update);
            $flag = $extends->save();
            //记录日志
            $log = new OrderLog();
            $log->setAttributes([
                'order_id' => $order->order_id,
                'before_order_state' => $before_order_state,
                'order_state' => $order->order_state,
                'log_msg' => '更新完善保单资料',
                'log_user' => $order['member_name'],
                'log_time' => date('Y-m-d H:i:s')
            ]);
            $log->save(false);
            if ($flag) {
                $tran->commit();
                return $this->getCheckYes([], '资料已提交，待审核');
            }
        } catch (Exception $e) {
            $tran->rollBack();
            return $this->getCheckNo($e->getMessage());
        }
        return $this->getCheckNo('完善资料失败,请重试');
    }
}
