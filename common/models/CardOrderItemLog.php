<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_card_order_item_log}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $content
 * @property integer $uid
 * @property string $name
 * @property string $update_time
 */
class CardOrderItemLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_card_order_item_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'uid'], 'integer'],
            [['content', 'uid', 'name'], 'required'],
            [['content'], 'string'],
            [['update_time'], 'safe'],
            [['name'], 'string', 'max' => 50],
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
            'content' => 'Content',
            'uid' => 'Uid',
            'name' => 'Name',
            'update_time' => 'Update Time',
        ];
    }


    /**
     * 增加日志
     * @param array $data
     * @return bool
     */
    public static function addLog($data=[]){

        $uid=Yii::$app->user->identity->id;
        $name =isset(Yii::$app->user->identity->name)?Yii::$app->user->identity->name:'平台管理员 '.Yii::$app->user->identity->username;
        $_self = new self;
        $_self->uid= $uid;
        $_self->name =$name;
        $_self->update_time = date('Y-m-d H:i:s');
        foreach($data as $key=>$val){
            $_self->$key=$val;
        }

        return $_self->save();
    }
}
