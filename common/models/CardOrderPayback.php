<?php

namespace common\models;

use common\library\helper;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%_card_order_payback}}".
 *
 * @property string $pay_id
 * @property string $pay_sn
 * @property integer $pay_status
 * @property integer $apply_type
 * @property integer $handle_type
 * @property string $from_seller_id
 * @property integer $to_seller_id
 * @property string $num
 * @property string $total_price
 * @property string $send_total_price
 * @property string $received_price
 * @property string $back_price
 * @property string $real_back_price
 * @property integer $add_time
 */
class CardOrderPayback extends \yii\db\ActiveRecord
{
    const WAIT_ISSUE = 0;
    //const READY_ISSUE = 1;
    const PART_RETURN_MONEY = 2;
    const ALL_RETURN_MONEY = 3;
    const CACEL_RETURN_MONEY = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_card_order_payback}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_sn', 'from_seller_id', 'add_time'], 'required'],
            [['pay_status', 'apply_type', 'handle_type', 'from_seller_id', 'to_seller_id', 'num', 'add_time'], 'integer'],
            [['total_price', 'send_total_price', 'received_price', 'back_price', 'real_back_price'], 'number'],
            [['pay_sn'], 'string', 'max' => 20],
            [['pay_sn'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_id' => 'Pay ID',
            'pay_sn' => 'Pay Sn',
            'pay_status' => 'Pay Status',
            'apply_type' => 'Apply Type',
            'handle_type' => 'Handle Type',
            'from_seller_id' => 'From Seller ID',
            'to_seller_id' => 'To Seller ID',
            'num' => 'Num',
            'total_price' => 'Total Price',
            'send_total_price' => 'Send Total Price',
            'received_price' => 'Received Price',
            'back_price' => 'Back Price',
            'real_back_price' => 'Real Back Price',
            'add_time' => 'Add Time',
        ];
    }


    public static function getMsg($status = 0)
    {
        $msg = '待付款';
        switch ($status) {
            case self::PART_RETURN_MONEY:
                $msg = ' 部分付款';
                break;
            case self::ALL_RETURN_MONEY :
                $msg = '全款';
                break;
            case self::CACEL_RETURN_MONEY :
                $msg = '已取消';
                break;
        }
        return $msg;
    }

    public static function statusData()
    {
        return [
            self::PART_RETURN_MONEY => '部分付款',
            self::ALL_RETURN_MONEY => '全款',
            self::WAIT_ISSUE => '待付款',
            self::CACEL_RETURN_MONEY => '已取消'
        ];
    }

    public static function getTypeMsg($status = 0)
    {
        $msg = '后付款';
        switch ($status) {
            case self::PART_RETURN_MONEY:
                $msg = ' 部分付款';
                break;
            case self::ALL_RETURN_MONEY :
                $msg = '全款';
                break;
        }
        return $msg;
    }

    public static function typeData()
    {
        return [
            self::PART_RETURN_MONEY => '部分付款',
            self::ALL_RETURN_MONEY => '全款',
            self::WAIT_ISSUE => '后付款'
        ];
    }


    /**
     * 检测并更新状态
     * @param string $order_sn
     */
    public static function checkStatus($order_sn = '')
    {
        $tj = ['pay_sn' => $order_sn];
        $result = CardOrderItem::findAll($tj);
        $bstop = [];
        foreach ($result as $val) {
            if ($val['status'] == 2) {
                $bstop['ok'][] = 1;
            } elseif ($val['status'] == 3) {
                $bstop['err'][] = 1;
            }
        }
        $num = count($result);
        $ok_num = count($bstop['ok']);
        $err_num = count($bstop['err']);
        $total = $err_num + $ok_num;
        if ($ok_num === $num || $total === $num) {
            $model = self::findOne($tj);
            $model->pay_status = 1;
            $model->save();
        } else if ($err_num == $num) {
            $model = self::findOne($tj);
            $model->pay_status = 4;
            $model->save();
        }
    }

    /**
     * 主动创建订单
     * @param array $data
     * @return bool|string
     */
    public static function create($data = [],$uid=0)
    {
    	$time =time();
        try {
            $model = new self;
            $model->pay_sn = helper::_makeOrderSn($uid);
            $model->pay_status = 1;
            $model->from_seller_id = $data['from_seller_id'];
            $model->to_seller_id = $data['to_seller_id'];
            $model->num = $data['num'];
            $model->total_price = $data['total_price'];
            $model->add_time = $time;
            if ($model->save()) {
                $item = new CardOrderItem();
                $item->pay_sn = $model->pay_sn;
                $item->coverage_code = $data['coverage_code'];
                $item->number = $model->num;
                $item->price = $data['price'];
                $item->status = 0;
                $item->add_time = $time;
				$item->send_time = $time;
                if ($item->save()) {
                    return $model->pay_sn;
                }
            }
            throw new Exception('错误');
        } catch (Exception $e) {
            return false;
        }
    }


    public function getOrders()
    {
        return $this->hasMany(CardOrderItem::className(), ['pay_sn' => 'pay_sn'])->all();
    }

}
