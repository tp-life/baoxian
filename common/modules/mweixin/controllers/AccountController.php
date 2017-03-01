<?php

namespace common\modules\mweixin\controllers;
use common\models\Area;
use common\models\BrandModel;
use common\models\Order;
use common\models\OrderExtend;
use common\models\OrderLog;
use common\models\OrderMaintenance;
use common\models\OrderMaintenanceService;
use common\models\Seller;
use common\wxpay\Wpay;
use common\wxpay\WxHelp;
use weixin\components\BaseController;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * api for 用户相关信息  处理  订单列表及详细
*/

class AccountController extends BaseController
{
	/**
	 * Renders the index view for the module
	 * @return string
	 */
	public function actionIndex()
	{
        $member_id= $this->member_id;
        $pageSize = Yii::$app->request->get('length', 50);
        $start = Yii::$app->request->get('start', 0);
        $filed = 'e.start_time,e.end_time,o.order_sn,o.order_id,o.member_id,o.add_time,o.member_name,o.order_amount,o.order_state,o.coverage_code,o.coverage_name,o.coverage_price,c.period,c.max_payment,c.image,m.state,e.err_code as bstop';
        $model = Order::getQuery(['o.member_id' => $member_id], $filed,'o.order_id desc,m.id desc');
        $count=$model->count('*');
        $dataProvider = new ActiveDataProvider([
            'query' =>$model->limit($pageSize)->offset($start)->asArray(),
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => intval($start / $pageSize),
                'totalCount' => $count
            ]
        ]);
        $data = $dataProvider->getModels();
        foreach ($data as &$item) {
            $item['start_time'] =$item['start_time']?date('Y-m-d',$item['start_time']):$item['start_time'];
            $item['end_time'] =$item['end_time']?date('Y-m-d',$item['end_time']):$item['end_time'];
            $item['add_time'] =date('Y-m-d',$item['add_time']);
            if(in_array($item['bstop'],[Order::__ERR_PHONE])){
                $item['bstop']=1;
            }else{
                $item['bstop']=0;
            }
        }
		return $this->getCheckYes($data);
	}


	public function actionDetail()
	{
		$order_id = Yii::$app->request->get('order_id', 0);
		$order_id = $order_id ? $order_id : $_REQUEST['order_id'];
		if (!$order_id) {
			return $this->getCheckNo('参数错误');
		}
        $filed = 'e.start_time,e.end_time,o.order_sn,o.order_id,o.add_time,o.member_id,e.buyer as member_name,o.order_amount,o.order_state,o.coverage_code,o.coverage_name,o.coverage_price,c.period,c.max_payment,c.image,m.state';
        $filed.=',m.type,m.info,m.add_time as xiu_time,m.province_id,m.city_id,m.area_id,m.address,e.buyer,e.imei_code,e.idcrad,e.buyer_phone,e.brand_id,e.model_id,e.err_code as bstop';
        $order = Order::getQuery(['o.order_id' => $order_id],$filed,'m.id desc')->asArray()->one();
        if($order['order_state'] == Order::__ORDER_ENSURE && $order['end_time'] < time() && $order['end_time'] > 0 ){
            $order['order_state'] = 90;
        }
        if($order['']){}
        $order['end_time'] =$order['end_time']?date('Y-m-d',$order['end_time']):$order['end_time'];
        $order['start_time'] =$order['start_time']?date('Y-m-d',$order['start_time']):$order['start_time'];
        $order['xiu_time'] =$order['xiu_time']?date('Y-m-d H:i',$order['xiu_time']):$order['xiu_time'];
        $order['add_time'] = date('Y-m-d H:i',$order['add_time']);
        $brand_ids=[$order['brand_id'],$order['model_id']];
        $brandInfo = BrandModel::find()->select('id,model_name')->where(['id'=>$brand_ids])->all();
        $order['brand']='';
        $order['model']='';
        $remark_info=OrderLog::find()->where(['order_id'=>$order_id,'status'=>1])->orderBy('log_id DESC')->one();
        $order['remark']=$remark_info?$remark_info->log_msg:'';
        $order['remark']= is_null($order['info'])?$order['remark']:$order['info'];
        if($brandInfo){
            foreach ($brandInfo as $item){
                if($item->id == $order['brand_id']){
                    $order['brand']=$item->model_name;
                }
                if($item->id == $order['model_id']){
                    $order['model']=$item->model_name;
                }
            }
        }

        if(in_array($order['bstop'],[Order::__ERR_PHONE])){
            $order['bstop']=1;
        }else{
            $order['bstop']=0;
        }
        $real_address = [];
        if($order['province_id']){
        	$real_address[] = Area::getInfo($order['province_id'],'area_name');
		}
		if($order['city_id']){
			$real_address[] = Area::getInfo($order['city_id'],'area_name');
		}
		if($order['area_id']){
			$real_address[] = Area::getInfo($order['area_id'],'area_name');
		}if($order['address']){
		$real_address[] = $order['address'];
		}
		if($real_address){
			$order['real_address'] = implode(' ',$real_address);
		}else{
			$order['real_address'] = '';
		}
		$order['seller_tel']='';
		if(in_array($order['state'],[OrderMaintenance::_MT_STATE_IN_SERVICE,OrderMaintenance::_MT_STATE_INFO_TO_BE_SUBMIT,OrderMaintenance::_MT_STATE_SUCCESS]) ){
            $order_server=OrderMaintenanceService::find()->where(['order_id'=>$order_id])->orderBy('id desc')->one();
            if($order_server && $seller = Seller::findOne(['seller_id'=>$order_server->m_id])){
                if($seller){
                    $order['seller_tel'] = $seller->concat_tel;
                }
            }
        }

        unset($order['brand_id']);
        unset($order['model_id']);
        return $order?$this->getCheckYes($order):$this->getCheckNo('保险详情获取失败');
	}

	public function actionCancel()
	{
		$order_id = Yii::$app->request->post('order_id', 0);
		$hasOrder = Order::findOne(['order_id' => $order_id, 'member_id' => $this->member_id]);
		if (!$hasOrder) {
			return $this->getCheckNo('查无保险订单信息');
		}
		if ($hasOrder['order_state'] == Order::__ORDER_CACEL) {
			return $this->getCheckYes([], '保险订单(' . $hasOrder['order_sn'] . ')已经取消');
		}
		$before_order_state = $hasOrder->order_state;
		$hasOrder->order_state = Order::__ORDER_CACEL;
		$hasOrder->buyer_msg = '自主取消此保险订单';
		if ($hasOrder->update(true, ['order_state', 'buyer_msg'])) {
			$log = [
				'order_id' => $hasOrder['order_id'],
				'before_order_state' => $before_order_state,
				'order_state' => Order::__ORDER_CACEL,
				'log_msg' => '保险订单(' . $hasOrder['order_sn'] . ')确认取消',
				'log_user' => $this->member_name,
				'log_time' => date('Y-m-d H:i:s')
			];
			$logObj = new OrderLog();
			$logObj->setAttributes($log);
			$logObj->save();
			return $this->getCheckYes([], '保险订单已经取消,编号：' . $hasOrder['order_sn']);
		}
		return $this->getCheckNo('保险订单暂不能取消');
	}

    /**
     * 获取订单完善资料信息
     * @return array
     */
    public function actionGetinfo(){
        $order_id = Yii::$app->request->get('order_id','');
        if(!$order_id){
            return $this->getCheckNo('参数错误');
        }
        $field ='imei_code,idcrad,brand_id,model_id,buyer,err_code as bstop,imei_face_image,imei_back_image';
        $order_info=OrderExtend::find()->select($field)->where(['order_id'=>$order_id])->asArray()->one();
        if(!$order_info){
            return $this->getCheckNo('当前订单不存在');
        }
        $order_info = $this->getMap($order_info);
        $brand_ids=[$order_info['brand_id'],$order_info['model_id']];
        $brandInfo = BrandModel::find()->select('id,model_name')->where(['id'=>$brand_ids])->all();
        if($brandInfo){
            foreach ($brandInfo as $item){
                if($item->id == $order_info['brand_id']){
                    $order_info['brand']=$item->model_name;
                }
                if($item->id == $order_info['model_id']){
                    $order_info['model']=$item->model_name;
                }
            }
        }
        if(in_array($order_info['bstop'],[Order::__ERR_IMEI,Order::__ERR_BRAND])){
            $order_info['bstop']=0;
        }else{
            $order_info['bstop']=1;
        }
        return $this -> getCheckYes($order_info);
    }


    private function getMap($order_info =[]){
        $data=['model'=>'','brand'=>'','imei_code'=>'','idcrad'=>'','brand_id'=>'','model_id'=>'','buyer'=>'','imei_back_image'=>'','imei_face_image'=>''];
        if(!is_array($order_info)){
            return $data;
        }
        return array_merge($data,$order_info);
    }

}
