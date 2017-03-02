<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_order_maintenance}}".
 *
 * @property string $id
 * @property string $member_id
 * @property integer $order_id
 * @property string $order_sn
 * @property integer $type
 * @property string $express_number
 * @property string $contact
 * @property string $contact_number
 * @property string $province_id
 * @property string $city_id
 * @property string $area_id
 * @property string $address
 * @property integer $state
 * @property string $verify_time
 * @property integer $is_finish
 * @property string $mark
 * @property string $add_time
 * @property string $before_time
 * @property string $appointment_date
 * @property string $appointment_time
 * @property string $real_appointment_time
 * @property integer $edit_num
 * @property string $phone_img
 * @property string $info
 */
class OrderMaintenance extends \yii\db\ActiveRecord
{

	/**
	 * 维修方式 1上门 2到店 3邮寄
	 */

	const _MT_TYPE_DOOR = 1;
	const _MT_TYPE_STORE = 2;
	const _MT_TYPE_MAIL = 3;

	public static function typeData()
	{
		return [
			self::_MT_TYPE_DOOR => '上门',
			self::_MT_TYPE_STORE => '到店',
			self::_MT_TYPE_MAIL => '邮寄'
		];
	}

	public function getTypeText()
	{
		$t = self::TypeData();
		return isset($t[$this->type]) ? $t[$this->type] : '';
	}

	/** 维保订单状态  客户 -平台-商家
	 * 0 失败--审核失败
	 * 1 提交理赔--待审核  默认
	 * 2 审核--待指派
	 * 3 指派人员--服务中
	 * 4 服务中--待提交理赔资料
	 * 5 维保完成
	 * 6 理赔清算

	To be appointed
	 * in service

	 *
	 * @note 维保完成 理赔并没有完成 还有 平台-商家 资料或结算处理  状态 详见model for OrderMaintenanceService
	 *
	 * @note 0 1 2 维保手动处理  其他状态 跟随 详见model for OrderMaintenanceService 更新
	 */

	const _MT_STATE_FAIL = 0;
	const _MT_STATE_TO_CHECK = 1; //default
	const _MT_STATE_TO_APPOINT = 2;
	const _MT_STATE_IN_SERVICE = 3;
	const _MT_STATE_INFO_TO_BE_SUBMIT = 4;
	const _MT_STATE_SUCCESS = 5;
	//const _MT_STATE_OVER = 6;//财务打款 最终理赔流程结束   ###已经取消 最终维保订单只有两种状态  5|7
	const _MT_STATE_SERVICE_FAIL = 7;//理赔失败，无法维修
	const _MT_STATE_SERVICE_RE_TO_APPOINT = 8;//商家无法维修打回 重新指派


	public static function stateData()
	{
		return [
			self::_MT_STATE_FAIL => '审核失败',
			self::_MT_STATE_TO_CHECK => '待审核',
			self::_MT_STATE_TO_APPOINT => '审核成功待指派',//审核成功
			self::_MT_STATE_IN_SERVICE => '服务中',//指派后服务中 或者提交资料待审核服务中
			self::_MT_STATE_INFO_TO_BE_SUBMIT => '待维修资料提交',
			self::_MT_STATE_SUCCESS => '维保完成',
			//self::_MT_STATE_OVER=>'理赔结清',
			self::_MT_STATE_SERVICE_FAIL=>'维修失败',
			self::_MT_STATE_SERVICE_RE_TO_APPOINT=>'维修失败待指派'
		];
	}

	public function getStateText()
	{
		$t = self::stateData();
		return isset($t[$this->state]) ? $t[$this->state] : '';
	}


	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_order_maintenance}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'order_id', 'type','express_id', 'province_id', 'city_id', 'area_id', 'state', 'verify_time', 'is_finish', 'add_time', 'edit_num'], 'integer'],
            [['order_id', 'contact', 'contact_number', 'province_id', 'city_id', 'area_id', 'address', 'mark'], 'required'],
            [['order_sn', 'contact', 'appointment_date', 'appointment_time'], 'string', 'max' => 50],
            [['express_number'], 'string', 'max' => 40],
            [['contact_number', 'address', 'mark', 'before_time', 'real_appointment_time', 'phone_img', 'back_img', 'id_back_img', 'id_face_img'], 'string', 'max' => 255],
            [['info'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'type' => 'Type',
			'express_id' =>'Express Id',
            'express_number' => 'Express Number',
            'contact' => 'Contact',
            'contact_number' => 'Contact Number',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'area_id' => 'Area ID',
            'address' => 'Address',
            'state' => 'State',
            'verify_time' => 'Verify Time',
            'is_finish' => 'Is Finish',
            'mark' => 'Mark',
            'add_time' => 'Add Time',
            'before_time' => 'Before Time',
            'appointment_date' => 'Appointment Date',
            'appointment_time' => 'Appointment Time',
            'real_appointment_time' => 'Real Appointment Time',
            'edit_num' => 'Edit Num',
            'phone_img' => 'Phone Img',
			'back_img' => 'back Img',
			'id_face_img' => 'Face Img',
			'id_back_img' => 'Id Bace Img',
            'info' => 'Info',
        ];
    }


	/**
	 * 所属用户
	*/
	public function getMemberInfo()
	{
		return $this->hasOne(Member::className(),['member_id'=>'member_id'])->one();
	}

	/**
	 * 商家处理信息
	 */
	public function getServiceInfo()
	{
		return $this->hasOne(OrderMaintenanceService::className(),['m_order_id'=>'id'])->orderBy('id DESC')->one();
	}

	/**
	 * 获取快递信息
	 */
	public function getExpressInfo()
	{
		if($this->type!==self::_MT_TYPE_MAIL){
			return null;
		}
		return $this->hasOne(Express::className(),['id'=>'express_id'])->one();
	}


	/**
	 * 日志信息
	*/
	public function getLogInfo($where = array())
	{
		return $this->hasMany(OrderMaintenanceLog::className(),['m_order_id'=>'id'])->where($where)->orderBy('id DESC')->all();
	}

	public function getAddressInfo()
	{
		$province = $this->hasOne(Area::className(),['area_id'=>'province_id'])->one();
		$city = $this->hasOne(Area::className(),['area_id'=>'city_id'])->one();
		$area = $this->hasOne(Area::className(),['area_id'=>'area_id'])->one();
		$t = [];
		if($province){
			$t[] = $province['area_name'];
		}
		if($city){
			$t[] = $city['area_name'];
		}
		if($area){
			$t[] = $area['area_name'];
		}
		return implode(' ',$t);
	}

	/**
	 * 更新订单状态
	*/
	public function changeOrderState($state, $note)
	{
		$this->state = $state;
		if($this->state == self::_MT_STATE_FAIL){
			$this->info = $note;
		}
		if ($this->update(false)) {
			$note = $this->getStateText().'#'.$note;
			OrderMaintenanceLog::addLog($this, $note);
			return true;
		}
		return false;
	}

	/**
	 * 商家指派
	**/
	public function zhipaiSeller($seller,$note,$params = [])
	{

		if($this->getServiceInfo()){
			//重新指派时 如果存在其他指派 要废弃已经指派的商家订单状态
			$int_row_updated = OrderMaintenanceService::updateAll(['service_status'=>OrderMaintenanceService::_MS_STATE_TO_DELETE,'server_mark'=>'系统重新指派，此指派单作废'],['m_order_id'=>$this->id]);
			if($int_row_updated<1){
				return false;
			}
		}
		$this->province_id = intval($params['province_id']);
		$this->city_id = intval($params['city_id']);
		$this->area_id = intval($params['area_id']);
		$this->address = trim($params['detail_address']);
		$this->state = self::_MT_STATE_IN_SERVICE;
		if($this->update(false)){
			$note_t = '指派商家:['.$seller['seller_name'].'|'.$seller['concat'].'|'.$seller['concat_tel'].']#'.$note;
			OrderMaintenanceLog::addLog($this, $note_t);
			$obj = new OrderMaintenanceService();
			$obj->order_id = $this->order_id;
			$obj->order_sn = $this->order_sn;
			$obj->m_order_id = $this->id;
			$obj->m_id = $seller['seller_id'];
			$obj->delivery_note = $note;
			$obj->add_time = time();
			$obj->save(false);

			//指派消息
			$insert = array();
			$insert['seller_id'] = $obj->m_id;
			$insert['m_order_id'] = $obj->id;
			$insert['type'] = Msg::_TYPE_ASSIGNED;

			$extention = OrderExtend::findOne(['order_id'=>$obj->order_id]);

			$proccess = array();
			$proccess['order_sn'] = $obj->order_sn;
			$proccess['brand_model'] = $extention?$extention->getPhoneInfo():'';

			Msg::addMessage($insert,$proccess);

			return true;
		}

		return false;
	}

	/**
	 * 理赔更新处理
	**/
	public function serviceLipei($serviceModel, $service_status, $note, $is_show,$serviceAttributes=array())
	{
		$serviceModel->service_status = $service_status;
		$serviceModel->manager_note = $note;
		if ($service_status == OrderMaintenanceService::_MS_STATE_TO_CHECK || $service_status == OrderMaintenanceService::_MS_STATE_CHECK_SUCCESS) {
			//维修完成 提交审核资料
			$serviceModel->repair_ok_time = time();
		}
		if ($serviceModel->update(false)) {

			//流单 理赔失败
			if ($service_status == OrderMaintenanceService::_MS_STATE_FAIL) {
				$this->state = self::_MT_STATE_SERVICE_RE_TO_APPOINT;//拒绝维修待指派
				$this->edit_num+=1;
				$this->update(false);
				$note_show = $this->getStateText();
				//OrderMaintenanceLog::addLog($this, $note_show);
				$note = $serviceModel->getStatusText() . '#' . $note;
				OrderMaintenanceLog::addLog($serviceModel, $note, 0);

			} elseif ($service_status == OrderMaintenanceService::_MS_STATE_INFO_TO_BE_SUBMIT) {
				//待理赔资料提交
				$this->state = self::_MT_STATE_INFO_TO_BE_SUBMIT;
				$this->update(false);
				$note_show = $this->getStateText();
				//OrderMaintenanceLog::addLog($this, $note_show);
				$note = $serviceModel->getStatusText() . '#' . $note;
				OrderMaintenanceLog::addLog($serviceModel, $note, $is_show);
			} elseif ($service_status == OrderMaintenanceService::_MS_STATE_TO_CHECK) {
				//待核查资料
				$this->state = self::_MT_STATE_SUCCESS;
				$this->update(false);
				$note_show = $this->getStateText();
				OrderMaintenanceLog::addLog($this, $note_show);
				$note = $serviceModel->getStatusText() . '#' . $note;
				OrderMaintenanceLog::addLog($serviceModel, $note, $is_show);
			}elseif ($service_status == OrderMaintenanceService::_MS_STATE_CHECK_SUCCESS) {
				//审核成功
				$this->state = self::_MT_STATE_SUCCESS;
				$this->update(false);
				$note_show = $this->getStateText();
				$note = $serviceModel->getStatusText() . '#' . $note_show;
				OrderMaintenanceLog::addLog($serviceModel, $note, 1);

				$s_d = array();
				$s_d['m_order_id'] = $serviceModel->m_order_id;
				$s_d['seller_id'] = $serviceModel->m_id;
				$s_d['price'] = $serviceModel->total_price;
				$s_d['expenses'] = $serviceModel->expenses;
				$s_d_log = [
					'content'=>'商家#'.$s_d['seller_id'].'#理赔可申请提现',
				];
				if( Yii::$app->user->identity instanceof Admin){
					$s_d_log['uid'] = Yii::$app->user->identity->id;
					$s_d_log['name'] = Yii::$app->user->identity->username;
				}
				SellerSettle::addSettle($s_d,$s_d_log);

			} else {
				$note = $serviceModel->getStatusText() . '#' . $note;
				OrderMaintenanceLog::addLog($this, $note, $is_show);
			}
			return true;
		}
		return false;

	}

}
