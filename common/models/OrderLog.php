<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_order_log}}".
 *
 * @property integer $log_id
 * @property integer $order_id
 * @property integer $before_order_state
 * @property integer $order_state
 * @property string $log_msg
 * @property string $log_user
 * @property string $log_time
 */
class OrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_order_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'before_order_state', 'order_state'], 'integer'],
            [['log_time'], 'safe'],
            [['log_msg'], 'string', 'max' => 150],
            [['log_user'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'order_id' => 'Order ID',
            'before_order_state' => 'Before Order State',
            'order_state' => 'Order State',
            'log_msg' => 'Log Msg',
            'log_user' => 'Log User',
            'log_time' => 'Log Time',

        ];
    }

    public  function insertLog(array $data = [])
    {
        foreach($data as $key=>$val){
            $this->$key=$val;
        }
        return $this->save();

    }
}
