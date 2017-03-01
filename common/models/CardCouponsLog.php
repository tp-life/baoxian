<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_card_coupons_log}}".
 *
 * @property string $id
 * @property integer $hand_type
 * @property string $seller_id
 * @property string $message
 * @property string $created
 */
class CardCouponsLog extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%_card_coupons_log}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['hand_type', 'from_seller_id', 'to_seller_id'], 'integer'],
			[['message'], 'required'],
			[['created'], 'safe'],
			[['message'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'hand_type' => 'Hand Type',
			'from_seller_id' => 'From Seller ID',
			'to_seller_id' => 'To Seller ID',
			'message' => 'Message',
			'created' => 'Created',
		];

	}


	const __TYPE_GRANT = 1;
	const __TYPE_ACTIVE = 2;
	const __TYPE_BACK = 3;

	public static function TypeData()
	{
		return [
			self::__TYPE_GRANT => '发放',
			self::__TYPE_ACTIVE => '激活',
			self::__TYPE_BACK => '退回'
		];
	}

	public function getTypeText()
	{
		$t = self::TypeData();
		return isset($t[$this->hand_type]) ? $t[$this->hand_type] : '';
	}

	public static function addLog($data = [])
	{
		$log = new CardCouponsLog();
		$log->attributes = $data;
		$log->created = date('Y-m-d H:i:s');
		return $log->insert(false);
	}


}
