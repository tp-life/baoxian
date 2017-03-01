<?php

namespace common\models;

use common\library\helper;
use Yii;

/**
 * This is the model class for table "{{%_msg}}".
 *
 * @property integer $id
 * @property integer $seller_id
 * @property integer $m_order_id
 * @property string $type
 * @property string $content
 * @property integer $status
 * @property integer $read_id
 * @property integer $read_time
 * @property integer $add_time
 */
class Msg extends \yii\db\ActiveRecord
{
	const _TYPE_WITHDRAWAL = 'withdrawal';//提现申请
	const _TYPE_PAYMENT = 'payment';//打款
	const _TYPE_ASSIGNED = 'assigned';//维修指派

	/**
	 * 消息类型定义
	 */
	public static function typeData()
	{
		return [
			self::_TYPE_WITHDRAWAL => '提现',
			self::_TYPE_PAYMENT => '打款',
			self::_TYPE_ASSIGNED => '指派'
		];
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_msg}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seller_id','send_id', 'm_order_id', 'status', 'read_id', 'read_time', 'add_time'], 'integer'],
            [['type'], 'string'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seller_id' => 'Seller ID',
            'm_order_id' => 'M Order ID',
            'type' => 'Type',
            'content' => 'Content',
            'status' => 'Status',
            'read_id' => 'Read ID',
            'read_time' => 'Read Time',
            'add_time' => 'Add Time',
            'send_id' =>'Send ID'
        ];
    }

    /**
     * 添加消息
     * @param array $data
     * @return bool
     */
    public function addMsg($data=[]){
        if(!$data) return false;
        if($data['type']){
            $data['content']=helper::handleMsg($data['type'],$data);
        }
        $map=[
            'add_time'=>date('Y-m-d H:i:s')
        ];
        $data=array_merge($map,$data);
        $attr=$this->attributes();
        foreach($data as $k=>$v){
            if(in_array($k,$attr)){
                $this->$k = $v;
            }
        }
        return $this->save();
    }

	/**
	 * @$attributeData  attributes
	 * @$proccessData   查看 helper
	*/
	public static function addMessage($attributeData = array(),$proccessData = array())
	{
		if(!in_array($attributeData['type'],array_keys(self::typeData()))){
			return false;
		}

		if(!isset($attributeData['content'])){
			$attributeData['content'] = helper::handleMsg($attributeData['type'],$proccessData);
		}
		$attributeData['add_time'] = time();
		$message = new Msg();
		$message->attributes = $attributeData;
		return $message->insert(false);
	}

}
