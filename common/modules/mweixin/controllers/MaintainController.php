<?php

namespace common\modules\mweixin\controllers;
use common\models\Order;
use common\models\OrderExtend;
use common\models\OrderMaintenance;
use weixin\components\BaseController;
use Yii;
/**
 * api for 保险维修 处理流程
*/

class MaintainController extends BaseController
{
    public function __construct($id, \yii\base\Module $module, array $config=[])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * 理赔申请提交
     * @return string
     */
    public function actionIndex()
    {
		$order_id = intval(Yii::$app->request->post('order_id',0));
		$phone_img = trim(Yii::$app->request->post('phone_img',''));
        $back_img = trim(Yii::$app->request->post('face_image',''));
        $id_back_image = trim(Yii::$app->request->post('id_back_image',''));
        $id_face_image = trim(Yii::$app->request->post('id_face_image',''));
//		$upload_phone_img = $this->updateFile('phone_img');
//		if ($upload_phone_img['code'] == 400) {
//			//return $upload_phone_img;
//			return $this->returnBack($upload_phone_img);
//		}
//		$phone_img = $upload_phone_img['path'];
//
//        $upload_back_image = $this->updateFile('face_image');
//        if ($upload_back_image['code'] == 400) {
//            //return $upload_back_image;
//			return $this->returnBack($upload_back_image);
//        }
//        $back_img = $upload_back_image['path'];
//
//        $upload_id_back_image = $this->updateFile('id_back_image');
//        if ($upload_id_back_image['code'] == 400) {
//            //return $upload_id_back_image;
//			return $this->returnBack($upload_id_back_image);
//        }
//        $id_back_image = $upload_id_back_image['path'];
//
//        $upload_id_face_image = $this->updateFile('id_face_image');
//        if ($upload_id_face_image['code'] == 400) {
//            //return $upload_id_face_image;
//			return $this->returnBack($upload_id_face_image);
//        }
//        $id_face_image = $upload_id_face_image['path'];

		$mark = trim(Yii::$app->request->post('mark',''));

		if (!$order_id) {
			return $this->getCheckNo('查无保障中保单记录');
		}
		if (!is_file(ltrim($phone_img,'/'))) {
			return $this->getCheckNo('手机正面照处理失败，请重试');
		}
		if (!is_file(ltrim($back_img,'/')) ) {
			return $this->getCheckNo('手机背面照处理失败，请重试');
		}
		if (!is_file(ltrim($id_back_image,'/'))) {
			return $this->getCheckNo('身份证背面照处理失败，请重试');
		}
		if (!is_file(trim($id_face_image,'/'))) {
			return $this->getCheckNo('身份证正面照处理失败，请重试');
		}

		if (!$mark) {
			return $this->getCheckNo('请填写问题描述');
		}

		$condition = ['member_id' => $this->member_id, 'order_state' => Order::__ORDER_ENSURE, 'order_id' => $order_id];
		$order = Order::findOne($condition);
		if (!$order) {
			return $this->getCheckNo('查无保障中保单记录');
		}

		$orderExtend = OrderExtend::findOne(['order_id'=>$order['order_id']]);
		//如果审核失败 就修改
		$orderMaintainObj = OrderMaintenance::findOne(['order_id'=>$order['order_id']]);

		if($orderMaintainObj && $orderMaintainObj['state']!=OrderMaintenance::_MT_STATE_FAIL){
			return $this->getCheckYes('正在处理理赔申请，不能重复提交');
		}
		if(empty($orderMaintainObj)){
			$orderMaintainObj = new OrderMaintenance();
		}
		$orderMaintainObj->setAttributes([
			'member_id'=>$this->member_id,
			'order_id'=>$order['order_id'],
			'order_sn'=>$order['order_sn'],
			'type'=>OrderMaintenance::_MT_TYPE_MAIL,//默认邮寄
			'contact'=>$orderExtend['buyer'],
			'contact_number'=>$orderExtend['buyer_phone'],
			'state'=>OrderMaintenance::_MT_STATE_TO_CHECK,//待审核
			'mark'=>$mark,
			'add_time'=>time(),
			'phone_img'=>$phone_img,
            'id_back_img'=>$id_back_image,
            'id_face_img'=>$id_face_image,
            'back_img'=>$back_img
		]);
		if($orderMaintainObj->save(false)){
			return $this->getCheckYes('理赔申请成功，待处理');
		}
		return $this->getCheckNo('理赔申请失败');

    }


	private $_problem_list = [
		'屏幕碎裂手机能正常使用',
		'屏幕碎裂手机无法正常使用',
	];
	/**
	 * 理赔维修问题选择
	*/
	public function actionProblem()
	{
		return $this->getCheckYes($this->_problem_list);
	}

}
