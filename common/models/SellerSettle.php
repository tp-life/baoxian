<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_seller_settle}}".
 *
 * @property string $id
 * @property integer $m_order_id
 * @property string $seller_id
 * @property string $price
 * @property integer $expenses
 * @property string $settle_time
 * @property string $add_time
 * @property integer $status
 * @property string $finsh_time
 */
class SellerSettle extends \yii\db\ActiveRecord
{
    const SETTLE_WAIT = 1; // 待结算
    const SETTLE_LOAD = 0; // 结算中
    const SETTLE_SUCC = 2; //结算成功
    const SETTLE_ERR = 3;   //结算失败

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_seller_settle}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['m_order_id', 'seller_id', 'expenses', 'settle_time', 'add_time', 'status', 'finsh_time'], 'integer'],
            [['price'], 'number'],
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
            'price' => 'Price',
            'expenses' => 'Expenses',
            'settle_time' => 'Settle Time',
            'add_time' => 'Add Time',
            'status' => 'Status',
            'finsh_time' => 'Finsh Time',
        ];
    }

    /**
     * 批量插入日志
     * @param string $id 结算ID
     * @param string $content 消息内容
     * @return bool
     */
    public function setLog($id='',$content='',$name='',$type='payment',$map=[]){
        $ids=is_array($id)?$id:(strpos($id,',') !==false ?explode(',',$id):[(int)$id]);
        $s=true;
        foreach($ids as $v){
            $info=$this->findOne(['id'=>$v]);
            if(!$info) continue;
            $log=[
                'seller_id'=>$info->seller_id,
                'm_order_id'=>$info->m_order_id,
                'price'=>number_format($info->price - $info->price * $info->expenses/100,2),
                'content'=>$content,
                'name' =>$name
            ];
            $msg=[
                'm_order_id'=>$info->m_order_id,
                'seller_id'=>$info->seller_id,
                'type'=>$type,
                'add_time'=>time(),
                'price'=>number_format($info->price - $info->price * $info->expenses/100,2),
                'seller_name'=>$name,
            ];
            $msg=array_merge($msg,$map);
            $model_log=new SellerSettleLog();
            $model_msg=new Msg();
            $model_msg->addMsg($msg);
            $s=$model_log->addLog($log);
        }
        return $s;
    }

    public static function getStatusMsg($staus = 1){
        if(!is_numeric($staus)){
            $staus = 1 ;
        }
        switch($staus) {
            case self::SETTLE_WAIT :
                return '待提现';
            case self::SETTLE_LOAD :
                return '申请提现中';
            case self::SETTLE_SUCC :
                return '提现成功';
            case self::SETTLE_ERR :
                return '提现失败';
            default :
                return '提现中';
        }
    }

	/**
	 * 审核通过 添加财务确认打款处理
	 **/
	public static function addSettle($data = [], $data_log = [])
	{
		$obj = new SellerSettle();
		$data['add_time'] = time();
		$data['settle_time'] = strtotime("+15 days",$data['add_time']);
		$data['status'] = self::SETTLE_WAIT;

		$obj->setAttributes($data);
		if ($obj->save()) {
			/*$log = new SellerSettleLog();
			$data_log['add_time'] = time();
			$data_log['m_order_id'] = $obj->m_order_id;
			$data_log['seller_id'] = $obj->seller_id;
			$data_log['price'] = $obj->price;
			$log->setAttributes($data_log);
			$log->save(false);*/
			return $obj;
		}
		return false;
	}



}
