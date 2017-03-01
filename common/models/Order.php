<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fj_order".
 *
 * @property integer $order_id
 * @property string $order_sn
 * @property integer $member_id
 * @property string $member_phone
 * @property string $payment_code
 * @property integer $payment_time
 * @property string $order_amount
 * @property integer $order_state
 * @property integer $coverage_id
 * @property string $coverage_code
 * @property string $coverage_name
 * @property integer $coverage_type
 * @property string $coverage_price
 * @property integer $is_delay
 * @property integer $number
 * @property integer $is_pre_vest_order
 * @property string $buyer_msg
 * @property integer $order_type
 * @property integer $order_from
 * @property integer $add_time
 */
class Order extends \yii\db\ActiveRecord
{
    const __ORDER_DEFAULT = 10,__ORDER_CACEL=0,__ORDER_PAY = 20,__ORDER_APPLF = 21,__ORDER_APPLF_SUCCESS = 22,
        __ORDER_APPLF_ERR = 23,__ORDER_ENSURE =30,__ORDER_COMPLETE =40,__ORDER_REFUND =60,__ORDER_REFUND_SUCC = 70,
        __ORDER_TO_CACEL = 80;

    //具体审核失败原因
    const __ERR_PHONE = 1,__ERR_IMEI =2,__ERR_BRAND =3,__ERR_OTHER =0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'member_id', 'member_phone', 'add_time'], 'required'],
            [['member_id', 'payment_time', 'order_state', 'coverage_id', 'coverage_type', 'is_delay', 'number', 'is_pre_vest_order', 'order_type', 'order_from', 'add_time'], 'integer'],
            [['order_amount', 'coverage_price'], 'number'],
            [['order_sn','member_name','pay_sn'], 'string', 'max' => 50],
            [['member_phone'], 'string', 'max' => 11],
            [['payment_code'], 'string', 'max' => 10],
            [['coverage_code'], 'string', 'max' => 40],
            [['coverage_name', 'buyer_msg'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'member_id' => 'Member ID',
            'member_name'=>'Member Name',
            'member_phone' => 'Member Phone',
            'payment_code' => 'Payment Code',
            'payment_time' => 'Payment Time',
            'order_amount' => 'Order Amount',
            'order_state' => 'Order State',
            'coverage_id' => 'Coverage ID',
            'coverage_code' => 'Coverage Code',
            'coverage_name' => 'Coverage Name',
            'coverage_type' => 'Coverage Type',
            'coverage_price' => 'Coverage Price',
            'is_delay' => 'Is Delay',
            'number' => 'Number',
            'is_pre_vest_order' => 'Is Pre Vest Order',
            'buyer_msg' => 'Buyer Msg',
            'order_type' => 'Order Type',
            'order_from' => 'Order From',
            'add_time' => 'Add Time',

        ];
    }

    public static function errMsg($code=''){
        $msg =[
            self::__ERR_PHONE =>'照片不符合要求',
            self::__ERR_IMEI =>'IMEI号码错误',
            self::__ERR_BRAND =>'品牌型号错误',
            self::__ERR_OTHER =>'其他'
        ];
        return isset($msg[$code])?$msg[$code]:'';
    }

    public function  getStatus($order=array()){
        if($order['end_time'] < time() && $order['end_time'] > 0){
            return '已过保';
        }else if($order['order_state'] == self::__ORDER_DEFAULT){
            return '待付款';
        }else if($order['order_state'] == self::__ORDER_ENSURE){
            return '保障中';
        }else if($order['order_state'] == self::__ORDER_CACEL){
            return '已取消';
        }else if($order['order_state'] == self::__ORDER_PAY){
            return '待完善';
        }else if($order['order_state'] == self::__ORDER_APPLF){
            return '待审核';
        }else if($order['order_state'] == self::__ORDER_APPLF_SUCCESS){
            return '审核成功';
        }else if($order['order_state'] == self::__ORDER_APPLF_ERR){
            return '审核失败';
        }else if($order['order_state'] == self::__ORDER_COMPLETE){
            return '已完成';
        }
        return '';
    }

    public static function getBackendStatusData()
	{
		return [
			self::__ORDER_DEFAULT=>'待付款',
			self::__ORDER_PAY=>'待完善',
			self::__ORDER_APPLF=>'待审核',
			self::__ORDER_APPLF_SUCCESS=>'审核成功',
			self::__ORDER_APPLF_ERR=>'审核失败',
			self::__ORDER_CACEL=>'已取消',
			self::__ORDER_ENSURE=>'保障中',
			32=>'已过保',
		];

	}


	/**
     * 订单列表
     * @param array $tj
     * @param string $filed
     * @param string $order
     * @param string $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOrder($tj=[],$filed="*",$order="o.order_id desc",$limit=''){
        return static::getQuery($tj,$filed,$order)->limit($limit)->asArray()->all();

    }

    /**
     * 查询语句
     * @param array $tj
     * @param string $filed
     * @param string $order
     *
     */

    public static function getQuery($tj=[],$filed="*",$order="o.order_id desc"){
        $tb=self::tableName();
        return self::find()->select($filed)->from(['o'=>$tb])->leftJoin(['e'=>OrderExtend::tableName()],"e.order_id = o.order_id")
            ->leftJoin(['m'=>OrderMaintenance::tableName()],"m.order_id = o.order_id")
            ->innerJoin(['c'=>InsuranceCoverage::tableName()],"c.id = o.coverage_id")
            ->where($tj)->orderBy($order);
    }

    /**
     * 查询订单详情信息语句
     * @param array $tj
     * @param string $filed
     * @param string $order
     * @return $this
     */
    public static function getOrderInfo($tj=[],$filed="*",$order="o.order_id desc"){
        $tb=self::tableName();
        return self::find()->select($filed)->from(['o'=>$tb])->innerJoin(['e'=>OrderExtend::tableName()],"e.order_id = o.order_id")
            ->where($tj)->orderBy($order);
    }
}
