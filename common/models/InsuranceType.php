<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fj_insurance_type".
 *
 * @property integer $id
 * @property string $type_name
 * @property string $type_code
 * @property string $note
 * @property integer $insurance_number
 * @property integer $status
 * @property integer $created
 */
class InsuranceType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_insurance_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name', 'type_code'], 'required'],
            [['insurance_number', 'status', 'created'], 'integer'],
            [['type_name'], 'string', 'max' => 100],
            [['type_code'], 'string', 'max' => 10],
            [['note'], 'string', 'max' => 255],
            [['type_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_name' => 'Type Name',
            'type_code' => 'Type Code',
            'note' => 'Note',
            'insurance_number' => 'Insurance Number',
            'status' => 'Status',
            'created' => 'Created',
        ];
    }
}
