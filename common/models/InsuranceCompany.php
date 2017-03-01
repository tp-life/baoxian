<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%insurance_company}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $logo
 * @property string $sp
 * @property string $contact_name
 * @property string $contact_phone
 * @property integer $p_id
 * @property integer $c_id
 * @property integer $a_id
 * @property string $address_detail
 * @property string $note
 * @property integer $insurance_number
 * @property integer $status
 * @property integer $created
 */
class InsuranceCompany extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_insurance_company}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sp', 'contact_name', 'contact_phone', 'p_id', 'c_id', 'a_id', 'address_detail'], 'required'],
            [['p_id', 'c_id', 'a_id', 'status','insurance_number', 'created'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [[ 'logo','address_detail'], 'string', 'max' => 200],
            [['sp', 'note'], 'string', 'max' => 255],
            [['contact_name', 'contact_phone'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'logo' => 'Logo',
            'sp' => 'Sp',
            'contact_name' => 'Contact Name',
            'contact_phone' => 'Contact Phone',
            'p_id' => 'P ID',
            'c_id' => 'C ID',
            'a_id' => 'A ID',
            'address_detail' => 'Address Detail',
            'note' => 'Note',
            'insurance_number' => 'Insurance Number',
            'status' => 'Status',
            'created' => 'Created',
        ];
    }

}
