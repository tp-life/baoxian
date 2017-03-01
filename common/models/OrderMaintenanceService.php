<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%_order_maintenance_service}}".
 *
 * @property string $id
 * @property string $order_id
 * @property string $order_sn
 * @property string $m_order_id
 * @property string $m_id
 * @property string $delivery_note
 * @property string $manager_note
 * @property integer $service_status
 * @property string $service_examine_time
 * @property string $vertify_img
 * @property string $vertify_result
 * @property integer $damage_type
 * @property string $inner_price
 * @property string $outer_price
 * @property integer $expenses
 * @property string $total_price
 * @property string $repair_ok_time
 * @property string $server_mark
 * @property string $add_time
 */
class OrderMaintenanceService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_order_maintenance_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'm_order_id', 'm_id', 'service_status', 'service_examine_time', 'damage_type', 'expenses', 'repair_ok_time', 'add_time'], 'integer'],
            [['vertify_img', 'vertify_result'], 'required'],
            [['vertify_img'], 'string'],
            [['inner_price', 'outer_price', 'total_price'], 'number'],
            [['order_sn'], 'string', 'max' => 50],
            [['delivery_note'], 'string', 'max' => 300],
            [['manager_note', 'vertify_result'], 'string', 'max' => 255],
            [['server_mark'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'm_order_id' => 'M Order ID',
            'm_id' => 'M ID',
            'delivery_note' => 'Delivery Note',
            'manager_note' => 'Manager Note',
            'service_status' => 'Service Status',
            'service_examine_time' => 'Service Examine Time',
            'vertify_img' => 'Vertify Img',
            'vertify_result' => 'Vertify Result',
            'damage_type' => 'Damage Type',
            'inner_price' => 'Inner Price',
            'outer_price' => 'Outer Price',
            'expenses' => 'Expenses',
            'total_price' => 'Total Price',
            'repair_ok_time' => 'Repair Ok Time',
            'server_mark' => 'Server Mark',
            'add_time' => 'Add Time',
        ];
    }

	/** 维保订单状态 平台-商家
	 * 1 待上门 2 服务中  3 流单（失败）  4待提交资料 5  审核中  6  审核失败  7 审核完成
	 *
	 * @note 维保完成 理赔并没有完成 还有 平台-商家 资料或结算处理  状态 详见model for OrderMaintenanceService
	 */
	const _MS_STATE_TO_DELETE = 0;//废弃指派处理 相当于删除
	const _MS_STATE_TO_DOOR = 1;//默认状态
	const _MS_STATE_IN_SERVICE = 2;
	const _MS_STATE_FAIL = 3;//维修失败 不能维修 打回平台
	const _MS_STATE_INFO_TO_BE_SUBMIT = 4;//服务中 待理赔资料提交
	const _MS_STATE_TO_CHECK = 5;//待核查资料
	const _MS_STATE_TO_CHECK_FAIL = 6;//核查不通过
	const _MS_STATE_CHECK_SUCCESS = 7;//审核完成

	public static function serviceStateData()
	{
		return [
			self::_MS_STATE_TO_DOOR => '待处理',//带上门 服务
			self::_MS_STATE_IN_SERVICE => '服务中',
			self::_MS_STATE_FAIL => '维修失败',
			self::_MS_STATE_INFO_TO_BE_SUBMIT => '待理赔资料提交',//服务中
			self::_MS_STATE_TO_CHECK => '待核查资料',
			self::_MS_STATE_TO_CHECK_FAIL => '核查未通过',
			self::_MS_STATE_CHECK_SUCCESS => '核查通过'

		];
	}

	/**
	 * 商家能够处理的状态 汇
	*/
	public static function showSellerState()
	{
		return [
			self::_MS_STATE_TO_DOOR => '待处理&nbsp;<font color="#ff7f50">(默认-刚指派完成)</font>',
			self::_MS_STATE_IN_SERVICE => '服务中&nbsp;<font color="#ff7f50">(在维修理赔进行中)</font>',
			self::_MS_STATE_FAIL => '维修失败&nbsp;<font color="#ff7f50">(商家发现客户资料和真实手机描述不符或存在骗保及其他行为)</font>',
			self::_MS_STATE_INFO_TO_BE_SUBMIT => '维修资料提交&nbsp;<font color="#ff7f50">（维修完成后商家提交相关资料）</font>'
		];

	}

	public function getStatusText()
	{
		$t = self::serviceStateData();
		return isset($t[$this->service_status]) ? $t[$this->service_status] : '';
	}


	//屏幕损坏 1 内屏 2 外屏  3内外屏
	const _PM_TYPE_INNER =1;
	const _PM_TYPE_OUTER =2;
	const _PM_TYPE_ALL =3;

	/**
	 * 报价类型
	*/
	public static function baojiaType()
	{
		return [
			self::_PM_TYPE_INNER=>'内屏&nbsp;<font color="#ff7f50">(以对应报价选项内屏计算)</font>',
			self::_PM_TYPE_OUTER=>'外屏&nbsp;<font color="#ff7f50">(以对应报价选项外屏计算)</font>',
			self::_PM_TYPE_ALL=>'内外屏&nbsp;<font color="#ff7f50">(以对应报价选项内屏+外屏计算)</font>'
		];
	}

	public function getBaojiaTypeText()
	{

		$t = self::baojiaType();
		return isset($t[$this->damage_type]) ? $t[$this->damage_type] : '';
	}


	/**
	 * 指派商家信息
	*/
	public function getSellerInfo()
	{
		return $this->hasOne(Seller::className(),['seller_id'=>'m_id'])->one();
	}

	/**
	 * 理赔订单信息
	 */
	public function getOrderInfo()
	{
		return $this->hasOne(OrderMaintenance::className(),['id'=>'m_order_id'])->one();
	}

	/**
	 * 保险订单详情
	*/
	public function getInsuranceOrderInfo()
	{
		return $this->hasOne(Order::className(),['order_id'=>'order_id'])->one();
	}


	/**
	 * 商家维保提交资料定义
	 * [
	 * before_phone_image         维修之前照片
	 * after_phone_image          维修之后照片
	 * old_and_new_screnn_image   新旧屏幕照片
	 * id_card_image              身份证照片
	 * repair_order_image         维修工单照片
	 * payable_image              维修发票照片
	 *
	 * ]
	*/

	static $verfiyImage = [
		'before_phone_image' => [
			'href' => '',
			'name' => '维修之前照片'
		],
		'after_phone_image' => [
			'href' => '',
			'name' => '维修之后照片'
		],
		'old_and_new_screnn_image' => [
			'href' => '',
			'name' => '新旧屏幕照片'
		],
	/*	'id_card_image' => [
			'href' => '',
			'name' => '身份证照片'
		],*/
		'repair_order_image' => [
			'href' => '',
			'name' => '维修工单照片'
		],
		'payable_image' => [
			'href' => '',
			'name' => '维修发票照片'
		]
	];

	public function getVerfiyImageInfo()
	{
		if($this->vertify_img){
			if($data = Json::decode($this->vertify_img,true)){
				return $data;
			}
		}
		return self::$verfiyImage;

	}
	/**
	 * @param $data = $verfiyImage  定义
	*/
	public function setVerfiyImageInfo($data = array())
	{
		$this->vertify_img = json_encode($data);
	}

}
