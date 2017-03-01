<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_seller_settle_log}}".
 *
 * @property string $id
 * @property string $m_order_id
 * @property string $seller_id
 * @property string $seller_name
 * @property string $price
 * @property string $content
 * @property integer $uid
 * @property string $name
 * @property string $add_time
 */
class SellerSettleLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_seller_settle_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['m_order_id'], 'required'],
            [['m_order_id', 'seller_id', 'uid'], 'integer'],
            [['price'], 'number'],
            [['add_time'], 'safe'],
            [['content'], 'string', 'max' => 255],
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
            'm_order_id' => 'M Order ID',
            'seller_id' => 'Seller ID',
            'seller_name' => 'Seller Name',
            'price' => 'Price',
            'content' => 'Content',
            'uid' => 'Uid',
            'name' => 'Name',
            'add_time' => 'Add Time',
        ];
    }

    /**
     * 新增日志
     * @param array $data
     * @return bool
     */
    public  function addLog(array $data=[]){
        $map=[
            'add_time'=>date('Y-m-d H:i:s'),
            'uid' =>Yii::$app->user->identity->id,
        ];
        $data=array_merge($map,$data);
        foreach($data as $key=>$val){
            $this->$key =$val;
        }
        return $this->save();
    }


}
