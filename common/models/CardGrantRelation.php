<?php

namespace common\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%_card_grant_relation}}".
 *
 * @property integer $id
 * @property integer $card_id
 * @property string $card_number
 * @property integer $order_id
 * @property string $pay_sn
 * @property integer $from_seller_id
 * @property integer $to_seller_id
 * @property integer $add_time
 * @property integer $deadline
 */
class CardGrantRelation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_card_grant_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_id', 'card_number', 'pay_sn', 'add_time'], 'required'],
            [['card_id', 'order_id', 'from_seller_id', 'to_seller_id', 'add_time', 'deadline'], 'integer'],
            [['card_number'], 'string', 'max' => 13],
            [['pay_sn'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_id' => 'Card ID',
            'card_number' => 'Card Number',
            'order_id' => 'Order ID',
            'pay_sn' => 'Pay Sn',
            'from_seller_id' => 'From Seller ID',
            'to_seller_id' => 'To Seller ID',
            'add_time' => 'Add Time',
            'deadline' => 'Deadline',
        ];
    }

    /**
     * 卡券发放
     * @param array $cards
     * @param array $data
     * @return bool
     */
    public function  createCard($cards=[],$data=[]){
        $from_seller_id = $data['from_seller_id'];
        $to_seller_id = $data['to_seller_id'];
        $pay_sn = $data['pay_sn'];
        $bind_val=[];
        $sql='INSERT INTO '.self::tableName().' (`card_id`,`card_number`,`order_id`,`pay_sn`,`from_seller_id`,`to_seller_id`,`add_time`,`deadline`) values  ';
        $str='';
        $time = date('Y-m-d H:i:s',time());
        foreach($cards as $k=>$val){
            $key_cid=':card_id_'.$k;
            $key_num=':card_number_'.$k;
            $key_c_time=':add_time_'.$k;
            $key_d_time =':deadline_'.$k;
            $sql .="( {$key_cid},{$key_num},:order_id,:pay_sn,:from_id,:to_id,{$key_c_time},{$key_d_time} ),";
            $bind_val[$key_cid] = $val['id'];
            $bind_val[$key_num]=$val['card_number'];
            $bind_val[$key_c_time]=time();
            $bind_val[$key_d_time]=$data['t']?strtotime('+'.$data['t'].'  day'):0;
            $msg = '发放险种(' . $val['coverage_code'] . ') (' . $val['card_number'] . ')从[' . $from_seller_id . '] 到商家[' . $to_seller_id . ']';
            $str .= '(3,'.$from_seller_id.','.$to_seller_id.',"'.$msg.'","'.$time.'"),';
        }
        $sql_log = 'INSERT INTO '.CardCouponsLog::tableName().' (`hand_type`,`from_seller_id`,`to_seller_id`,`message`,`created`) VALUES'.trim($str,',');
        $bind_val[':order_id']=$data['order_id']?$data['order_id']:0;
        $bind_val[':pay_sn'] = $pay_sn;
        $bind_val[':from_id'] = $from_seller_id;
        $bind_val[':to_id'] = $to_seller_id;
        $sql=rtrim($sql,',');
        try{
            $ret = Yii::$app->getDb()->createCommand($sql, $bind_val)->execute();
            Yii::$app->getDb()->createCommand($sql_log)->execute();
            $bstop = false;
            if($data['order_id']){
                $card_item = CardOrderItem::findOne(['order_id' => $data['order_id']]);
                if(!$card_item) throw new Exception('当前申请险种不存在！');
                $card_item -> status = 2 ;
                $bstop= $card_item ->save();
                $order_payback = CardOrderPayback::findOne(['pay_sn'=>$pay_sn]);
                if($order_payback){
                    $order_payback -> send_total_price = $card_item->number * $card_item->price;
                    $back=$order_payback->save();
                }else{
                    throw new Exception('当前申领订单不存在');
                }
            }
            if($ret && $bstop && $back){
                $arr=[
                    'order_id'=>$data['order_id'],
                    'content' =>isset($data['content'])?$data['content']:''
                ];
                CardOrderItemLog::addLog($arr);
                return true;
            }
            throw new Exception('错误');
        }catch (Exception $e){
            return false;
        }
    }


    /**
     * 检测指定的卡券是否全部在指定的商家中
     * @param array $cards
     * @param string $seller_id
     * @return bool
     */
    public static function checkIssue($cards=[],$seller_id=''){
        $result = self::getLastCards($cards);
        $bstop= true;
        foreach($result as $val){
            if($val['to_seller_id'] != $seller_id){
                $bstop = false;
                break;
            }
        }
        return $bstop;
    }

    /**
     * 获取最后一条卡券信息
     * @param array $cards
     * @return array
     */
    public static function getLastCards($cards=[]){
        $tb=self::tableName();
        $cards = array_map(function($card){
            return "'".$card."'";
        },$cards);
        $sql ='select * from ( SELECT  * FROM '.$tb.' where `card_number` in ( '.join(',',$cards).' ) order by id desc) t  group by `card_number` order by id desc';
        return Yii::$app->getDb()->createCommand($sql)->queryAll();
    }

    /**
     * 检查当前卡券是否属于当前发放订单
     * @param array $card
     * @param int $order_id
     * @return array|bool
     */
    public static function checkOrderCard($card=[],$order_id=0){
        if(!$card || !$order_id){
            return false;
        }
        $selfCard=self::find()->select('card_number')->where(['order_id'=>$order_id,'card_number'=>$card])->asArray()->all();
        $orderCard = array_column($selfCard,'card_number');
        $diffCard = array_diff($card,$orderCard);
        return $diffCard;
    }

    /**
     * 获取每一个订单的最小有效期
     * @param array $cards
     * @return array
     */
    public static function getDeadline($order=''){
        $result=self::find()->where(['pay_sn'=>$order])->andWhere(['>','deadline',0])->min('deadline');
        return $result;
    }

    /**
     * 获取当前订单所有的卡券
     * @param string $order
     * @return array|\yii\db\ActiveRecord[]
     */
    public static  function  getOrderCard($order=''){
        $result=self::find()->where(['pay_sn'=>$order])->asArray()->all();
        return $result;
    }



}
