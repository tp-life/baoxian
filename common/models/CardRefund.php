<?php

namespace common\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%_card_refund}}".
 *
 * @property string $id
 * @property string $pay_sn
 * @property string $card_numbers
 * @property integer $number
 * @property string $total_price
 * @property string $back_price
 * @property string $real_back_price
 * @property integer $from_seller_id
 * @property integer $to_seller_id
 * @property integer $status
 * @property integer $add_time
 * @property string $issue_card_numbers
 * @property string $issue_number
 */
class CardRefund extends \yii\db\ActiveRecord
{
	const __RF_PRIFIX = 'RF';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_card_refund}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_sn', 'card_numbers', 'from_seller_id', 'to_seller_id', 'add_time'], 'required'],
            [['card_numbers', 'issue_card_numbers'], 'string'],
            [['number', 'from_seller_id', 'to_seller_id', 'status', 'add_time', 'issue_number'], 'integer'],
            [['total_price', 'back_price', 'real_back_price'], 'number'],
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
            'pay_sn' => 'Pay Sn',
            'card_numbers' => 'Card Numbers',
            'number' => 'Number',
            'total_price' => 'Total Price',
            'back_price' => 'Back Price',
            'real_back_price' => 'Real Back Price',
            'from_seller_id' => 'From Seller ID',
            'to_seller_id' => 'To Seller ID',
            'status' => 'Status',
            'add_time' => 'Add Time',
            'issue_card_numbers' => 'Issue Card Numbers',
            'issue_number' => 'Issue Number',
        ];
    }



	/**
	 * 退款编号
	 */
	public function getFormatId()
	{
		return sprintf(self::__RF_PRIFIX . '%08s', $this->id);
	}


	const _RF_STATE_TO_DO = 0;//默认状态  待处理
	const _RF_STATE_TO_WAIT = 1;
	const _RF_STATE_SUCCESS = 2;
	const _RF_STATE_FAIL = 3;


	public static function refundStateData()
	{
		return [
			self::_RF_STATE_TO_DO => '待处理',//带上门 服务
			self::_RF_STATE_TO_WAIT => '待确认',
			self::_RF_STATE_SUCCESS => '退回成功',
			self::_RF_STATE_FAIL => '退回取消',

		];
	}

	public function getStatusText()
	{
		$t = self::refundStateData();
		return isset($t[$this->status]) ? $t[$this->status] : '';
	}

	/**
	 * 日志信息
	 */
	public function getLogInfo($where = array())
	{
		return $this->hasMany(CardRefundLog::className(), ['refund_id' => 'id'])->where($where)->orderBy('id DESC')->all();
	}

	public function getFromSellerInfo()
	{
		return $this->hasOne(Seller::className(), ['seller_id' => 'from_seller_id'])->one();
	}

	public function getCardInfo($limt=20)
	{
		$number = explode(',', trim($this->card_numbers));
		if (!$number) {
			return null;
		}
		return CardCouponsGrant::find()->where(['card_number' => $number])->limit($limt)->all();
	}

    /**
     * 问题卡券信息
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public function getErrCardInfo($limit=20)
    {
        $number = explode(',', trim($this->issue_card_numbers));
        if (!$number) {
            return null;
        }
        return CardCouponsGrant::find()->where(['card_number' => $number])->limit($limit)->all();
    }

    /**
     * 未发放卡券总金额
     * @param string $pay_sn
     * @return int
     */
    public static  function getWaitSendPrice($pay_sn=''){
        if(!$pay_sn) return 0;
        $price= CardOrderItem::find()->where(['pay_sn'=>$pay_sn,'status'=>[CardOrderItem::_CD_STATE_TO_DO,CardOrderItem::_CD_STATE_TO_WAIT]])->sum('number * price');
        return intval($price);
    }

	/**
	 * 卡券退回处理
	 * // step
	 * //退回商家判断  退回平台
	 * //改变卡券状态  和更新卡券日志
	 * //退回商家判断  退回1级商家
	 * //更新归属关系  改变卡券状态
	 * //退回状态
	 * //退回日志
	 */
	public function cardRefund($note='',$err_cards=[])
	{
		$isLehuanxin = in_array($this->to_seller_id, Seller::$lehuanxin);
		$result = ['code' => 'no', 'message' => '操作失败'];
		$wait_numbers = explode(',', trim($this->card_numbers));
        $card_numbers = array_diff($wait_numbers,$err_cards);
        //当问题卡券等于退卡卡券时，将退卡订单标记为失败取消
        if(!$card_numbers){
            $this->refundAll($note,$wait_numbers,$err_cards);
            $result = ['code' => 'yes', 'message' => '所有卡券皆不可退回，当前退卡申请已取消。'];
            return $result;
        }
		$card_obj = null;
		$hasGrant = true;

		foreach ($card_numbers as $card_number) {
			$card = null;
			if ($isLehuanxin) {

				$card = CardCouponsGrant::find()->where("card_number=:card_number and seller_id=:seller_id and status!=:status",[':card_number' => $card_number, ':seller_id' => $this->from_seller_id, ':status'=>1])->one();
			} else {
				$card = CardCouponsGrant::find()->where("card_number=:card_number and status!=:status",[':card_number' => $card_number, ':status'=>1])->one();
				//存在发放关系
				$hasGrant = CardGrantRelation::find()->where(['card_number' => $card_number, 'from_seller_id' => $this->to_seller_id, 'to_seller_id' => $this->from_seller_id])->orderBy('id DESC')->one();
			}
			if ($card && $hasGrant) {
				$card_obj[] = $card;
			} else {
				$result['message'] = $card_number . '#卡券已被激活或有异常';
				break;
			}
		}
		if (count($card_numbers) != count($card_obj)) {
			return $result;
		}

		/** *****************   退回平台处理逻辑   ************************/
		if ($isLehuanxin) {

			$transaction = Yii::$app->db->beginTransaction();
			try{
//				foreach ($card_obj as $obj) {
//					$obj->seller_id = $this->to_seller_id;
//					$obj->status = CardCouponsGrant::__STATUS_DEFAULT;
//					$obj->created = date('Y-m-d H:i:s');
//					if ($obj->save(false)) {
//						$t_data = [
//							'hand_type'=>CardCouponsLog::__TYPE_BACK,
//							'from_seller_id'=>$this->to_seller_id,
//							'to_seller_id'=>$this->from_seller_id,
//							'message'=>'退回卡券#'.$obj['card_number']
//						];
//						CardCouponsLog::addLog($t_data);
//					}else{
//						throw new Exception('退回处理异常#'.$obj['card_number']);
//					}
//				}
                //计算当前卡券所价值的金额

                $back_price=CardCouponsGrant::getCountPrice($card_numbers);
//                var_dump($back_price);die();
				$this->status = self::_RF_STATE_TO_WAIT;
                $this->back_price = $back_price;
                $this ->issue_card_numbers = join(',',$err_cards);
                $this->issue_number = count($err_cards);
                $model = CardOrderPayback::findOne(['pay_sn'=>$this->pay_sn]);
                if(!$model){
                    throw new Exception('当前卡券批次不存在');
                }
                $model ->back_price += $back_price;
                $ret =  $model->save();
                if($this->save() && $ret){
					CardRefundLog::addLog($this->id,$note);
					$transaction->commit();
				}else{
					throw new Exception('退回异常,退回编号#'.$this->getFormatId());
				}

			}catch (Exception $e){
				$transaction->rollBack();
				$result['message'] = $e->getMessage();
				return $result;
			}
			$result = ['code' => 'yes', 'message' => '退回成功'];
			return $result;
		}

		/** *****************   退回一级商家处理逻辑   ************************/
		$transaction = Yii::$app->db->beginTransaction();
		try{
			foreach ($card_obj as $obj) {

				$obj_relation_card = new CardGrantRelation();
				$obj_relation_card->attributes = [
					'card_id'=>$obj->id,
					'card_number'=>$obj->card_number,
					'from_seller_id'=>$this->from_seller_id,
					'to_seller_id'=>$this->to_seller_id,
					'add_time'=>time()
				];

				if ($obj_relation_card->insert(false)) {
					$t_data = [
						'hand_type'=>CardCouponsLog::__TYPE_BACK,
						'from_seller_id'=>$this->to_seller_id,
						'to_seller_id'=>$this->from_seller_id,
						'message'=>'退回卡券#'.$obj['card_number']
					];
					CardCouponsLog::addLog($t_data);
				}else{
					throw new Exception('退回处理异常#'.$obj['card_number']);
				}
			}
			$this->status = self::_RF_STATE_SUCCESS;
            $this ->issue_card_numbers = join(',',$err_cards);
            $this->issue_number = count($err_cards);
            $rets=CardCouponsGrant::changeStatus($this->card_numbers,CardCouponsGrant::__STATUS_DEFAULT);
			if($this->update(false,['status','issue_card_numbers','issue_number']) && $rets){
				CardRefundLog::addLog($this->id,$note);
				$transaction->commit();
			}else{
				throw new Exception('退回异常,退回编号#'.$this->getFormatId());
			}

		}catch (Exception $e){
			$transaction->rollBack();
			$result['message'] = $e->getMessage();
			return $result;
		}
		$result = ['code' => 'yes', 'message' => '退回成功'];
		return $result;
	}


	/**
	 * 检查是否包含已经申请退款成功或者正在退卡的卡券
	 * @param array $cards
	 * @return array|false
	 */
	public static function checkRefund($cards = [], $seller_id = '')
	{
		$table = self::tableName();
		$sql = ' SELECT `card_numbers`,`status`,`add_time`,`id` FROM ' . $table . ' WHERE `status` < 3 AND `from_seller_id` = ' . $seller_id . '  AND (';
        $sql_1= ' AND ( ';
		foreach ($cards as $val) {
			$sql .= 'POSITION( "' . $val . '" IN `card_numbers`) OR ';
            $sql_1 .= '  locate( "' . $val . '", `issue_card_numbers`) <= 0 AND ';
		}
		$sql = rtrim($sql, ' OR');
        $sql_1 = rtrim($sql_1, ' AND');
		$sql .= ' )';
        $sql_1.=' OR  issue_card_numbers IS NULL )';
        $sql .= $sql_1;
		return Yii::$app->db->createCommand($sql)->queryAll();
	}

    /**
     * 检查是否为重复发放至当前商家的卡券
     * @param array $cards
     * @param string $seller_id
     * @return Array
     */
	public static function checkRe($cards = [], $seller_id = ''){
        $refund = static::checkRefund($cards,$seller_id);
        if(!$refund){
            return false;
        }
        $refund_cards = [];
        $temp=[];
        foreach ($refund as $val){
//            if($val['status'] != self::_RF_STATE_SUCCESS) continue;
            $c=explode(',',$val['card_numbers']);
            $refund_cards= array_merge($refund_cards,$c);
            $temp[$val['id']]=['cards'=>$c,'time'=>$val['add_time']];
        }
        $check_cards = array_intersect($cards,$refund_cards);
        $result = CardGrantRelation::getLastCards($check_cards);
        $err_card=[];
        foreach ($result as $val){
            foreach ($temp as $v){
                if(in_array($val['card_number'],$v['cards'])){
                    if($val['add_time'] < $v['time']){
                        $err_card[] = $val['card_number'];
                    }
                }
            }
        }
        return $err_card;
    }

    /**
     * 当问题卡券等于所有退卡卡券之后则取消当前退卡
     * @param string $note
     * @param array $cards
     * @param array $err_card
     * @return bool
     */
	private function refundAll($note='',$cards=[],$err_card=[]){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->status = self::_RF_STATE_FAIL;
            $this -> issue_card_numbers =join(',',$err_card);
            $this->issue_number =count($err_card);
            $ret=$this->update(false,['status','issue_card_numbers','issue_number']);
            CardRefundLog::addLog($this->id,$note);
            $ret2 = CardCouponsGrant::changeStatus($cards,CardCouponsGrant::__STATUS_DEFAULT);

            if($ret && $ret2){
                $transaction -> commit();
                return true;
            }
            throw  new Exception('失败');
        }catch (Exception $e){
            $transaction->rollBack();
            return false;
        }

    }


}
