<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fj_insurance_coverage".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $company_name
 * @property integer $type_id
 * @property string $type_name
 * @property string $coverage_name
 * @property integer $period
 * @property string $cost_price
 * @property string $official_price
 * @property string $wholesale_price
 * @property string $max_payment
 * @property string $coverage_code
 * @property integer $status
 * @property integer $is_more
 * @property integer $is_delay
 * @property string $image
 * @property integer $add_time
 */
class InsuranceCoverage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fj_insurance_coverage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'company_name', 'type_id', 'coverage_code'], 'required'],
            [['company_id', 'type_id', 'period', 'status', 'is_more', 'add_time'], 'integer'],
            [['cost_price', 'official_price', 'wholesale_price', 'max_payment'], 'number'],
            [['company_name', 'type_name'], 'string', 'max' => 100],
            [['coverage_name'], 'string', 'max' => 60],
            [['coverage_code'], 'string', 'max' => 40],
            [['image'], 'string', 'max' => 200],
            [['coverage_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'company_name' => 'Company Name',
            'type_id' => 'Type ID',
            'type_name' => 'Type Name',
            'coverage_name' => 'Coverage Name',
            'period' => 'Period',
            'cost_price' => 'Cost Price',
            'official_price' => 'Official Price',
            'wholesale_price' => 'Wholesale Price',
            'max_payment' => 'Max Payment',
            'coverage_code' => 'Coverage Code',
            'status' => 'Status',
            'is_more' => 'Is More',
            'image' => 'Image',
            'add_time' => 'Add Time',
        ];
    }

    public function getCoverage($id=0){
        return $this->findOne(['id'=>$id]);
    }
}
