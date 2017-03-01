<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fj_order_extend".
 *
 * @property integer $common_id
 * @property integer $order_id
 * @property integer $seller_id
 * @property string $seller_name
 * @property string $buyer
 * @property string $buyer_phone
 * @property string $imei_code
 * @property string $idcrad
 * @property integer $buy_date
 * @property string $invo_image
 * @property string $imei_face_image
 * @property string $imei_back_image
 * @property integer $brand_id
 * @property integer $model_id
 * @property integer $color_id
 * @property integer $is_data
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $yiwai_start_time
 * @property integer $yiwai_end_time
 * @property string $policy_number
 * @property string $surplus_value
 * @property string $server_mark
 */
class OrderExtend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_order_extend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'seller_id', 'buy_date', 'brand_id', 'model_id', 'color_id', 'is_data', 'start_time', 'end_time', 'yiwai_start_time', 'yiwai_end_time','err_code'], 'integer'],
            [['surplus_value'], 'number'],
            [['seller_name', 'imei_code', 'idcrad'], 'string', 'max' => 20],
            [['buyer', 'policy_number'], 'string', 'max' => 50],
            [['buyer_phone'], 'string', 'max' => 11],
            [['invo_image', 'imei_face_image', 'imei_back_image'], 'string', 'max' => 255],
            [['server_mark'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'common_id' => 'Common ID',
            'order_id' => 'Order ID',
            'seller_id' => 'Seller ID',
            'seller_name' => 'Seller Name',
            'buyer' => 'Buyer',
            'buyer_phone' => 'Buyer Phone',
            'imei_code' => 'Imei Code',
            'idcrad' => 'Idcrad',
            'buy_date' => 'Buy Date',
            'invo_image' => 'Invo Image',
            'imei_face_image' => 'Imei Face Image',
            'imei_back_image' => 'Imei Back Image',
            'brand_id' => 'Brand ID',
            'model_id' => 'Model ID',
            'color_id' => 'Color ID',
            'is_data' => 'Is Data',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'yiwai_start_time' => 'Yiwai Start Time',
            'yiwai_end_time' => 'Yiwai End Time',
            'policy_number' => 'Policy Number',
            'surplus_value' => 'Surplus Value',
            'server_mark' => 'Server Mark',
            'err_code'=>'ERR_CODE'
        ];
    }

	public function getBrand()
	{
		return $this->hasOne(BrandModel::className(),['id'=>'brand_id'])->one();
	}
	public function getModel()
	{
		return $this->hasOne(BrandModel::className(),['id'=>'model_id'])->one();
	}
	public function getColor()
	{
		return $this->hasOne(BrandModel::className(),['id'=>'color_id'])->one();
	}
	public function getPhoneInfo()
	{
		$t = array();
		if($b = $this->getBrand()){
			$t[] = $b['model_name'];
		}
		if($m = $this->getModel()){
			$t[] = $m['model_name'];
		}
		if($c = $this->getColor()){
			$t[] = $c['model_name'];
		}
		return trim(implode(' ',$t));
	}

    /**
     * 检查imei号是否被重复使用
     * @param string $imei
     * @return array|bool|null|\yii\db\ActiveRecord
     */
	public static  function checkImei($imei =''){
        $imei =trim($imei);
        if(!$imei) return false;
        $tb = self::tableName();
        return self::find()->from(['e'=>$tb])->innerJoin(['o'=>Order::tableName()],"e.order_id = o.order_id")
            ->where(['e.imei_code'=>$imei])->andWhere("o.order_state <> :cancel",[':cancel'=>Order::__ORDER_CACEL])->one();
	}

}
