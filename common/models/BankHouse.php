<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%_bank_house}}".
 *
 * @property integer $bankcard_id
 * @property string $bankcard_name
 * @property integer $sort
 * @property string $pic
 */
class BankHouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_bank_house}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bank_name'], 'required'],
            [['sort'], 'integer'],
            [['bank_name'], 'string', 'max' => 60],
            [['pic'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bank_id' => 'Bank ID',
            'bank_name' => 'Bank Name',
            'sort' => 'Sort',
            'pic' => 'Pic',
        ];
    }

	public static function getAll()
	{
		return self::find()->select('bank_id,bank_name')->orderBy('sort ASC')->asArray()->all();
	}
	public static function getBankNameList()
	{
		return ArrayHelper::map(self::getAll(),'bank_name','bank_name');
	}

}
