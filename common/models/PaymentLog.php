<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_payment_log}}".
 *
 * @property string $id
 * @property string $pay_sn
 * @property string $order_id
 * @property string $data
 * @property string $end_time
 * @property string $add_time
 * @property string $remark
 */
class PaymentLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_payment_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'add_time', 'remark'], 'required'],
            [['order_id', 'end_time', 'add_time'], 'integer'],
            [['pay_sn','open_id'], 'string', 'max' => 64],
            [['data'], 'string', 'max' => 1000],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pay_sn' => 'Pay Sn',
            'order_id' => 'Order ID',
            'data' => 'Data',
            'end_time' => 'End Time',
            'add_time' => 'Add Time',
            'remark' => 'Remark',
            'open_id'=>'OPEN_ID'
        ];
    }

    public   function insertLog(array $data = [])
    {
        foreach($data as $key=>$val){
            $this->$key=$val;
        }
        return $this->save();

    }
}
