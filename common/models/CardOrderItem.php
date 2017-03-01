<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%_card_order_item}}".
 *
 * @property integer $order_id
 * @property string $pay_sn
 * @property string $coverage_code
 * @property integer $number
 * @property string $price
 * @property integer $status
 * @property integer $add_time
 */
class CardOrderItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_card_order_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'status', 'add_time','send_time'], 'integer'],
            [['price'], 'number'],
            [['pay_sn'], 'string', 'max' => 20],
            [['coverage_code'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'pay_sn' => 'Pay Sn',
            'coverage_code' => 'Coverage Code',
            'number' => 'Number',
            'price' => 'Price',
            'status' => 'Status',
            'add_time' => 'Add Time',
			'send_time' => 'Send Time'
        ];
    }


	const _CD_STATE_TO_DO = 0;
	const _CD_STATE_TO_WAIT = 1;
	const _CD_STATE_SUCCESS = 2;
	const _CD_STATE_FAIL = 3;


	public static function itemStateData()
	{
		return [
			self::_CD_STATE_TO_DO => '待处理',
			self::_CD_STATE_TO_WAIT => '待确认',
			self::_CD_STATE_SUCCESS => '已发放',
			self::_CD_STATE_FAIL => '已取消',

		];
	}

	public function getStatusText()
	{
		$t = self::itemStateData();
		return isset($t[$this->status]) ? $t[$this->status] : '';
	}

	public function getCoverageInfo()
	{
		return $this->hasOne(InsuranceCoverage::className(),['coverage_code'=>'coverage_code'])->one();
	}

	public function getLogInfo()
	{
		return $this->hasMany(CardOrderItemLog::className(),['order_id'=>'order_id'])->all();
	}

    /**
     * 检查卡券数量与当前订单是否相匹
     * @param string $pay_sn
     * @param int $num
     * @return array
     */
    public static  function checkNumber($pay_sn='',$code='',$card=[]){
        $table=self::tableName();
        $t_b = CardOrderPayback::tableName();
        $model_card_item=CardOrderPayback::find()->select('*')->innerJoin($table,$t_b.'.pay_sn = '.$table.'.pay_sn')
            ->where([
                $table.'.pay_sn'=>$pay_sn,
                $table.'.coverage_code'=>$code,
                $table.'.status'=>[0,1]
            ])->asArray()->one();
        if(!$model_card_item){
            return ['status'=>false,'msg'=>'当前卡券申请不存在！'];
        }
        $num= count($card);
        if($num != $model_card_item['number']){
            return ['status'=>false,'msg'=>'商家领用卡券'.$model_card_item['number'].'张，当前分配卡券 '.$num .'张'];
        }
        $where=[
            'seller_id' => $model_card_item['from_seller_id'],
            'status' => 0,
            'coverage_code' => $model_card_item ['coverage_code'],
            'card_number' =>$card
        ];
        $count = CardCouponsGrant::find()->where($where)->count('id');
        if(!$count){
            return ['status'=>false,'msg'=>'当前卡券不可用！'];
        }else if($count < $num){
            return ['status'=>false,'msg'=>'当前输入的卡券中可用卡券小于商家购买数量！请检查是否包含已经发放过的卡券'];
        }

        return ['status'=>true,'data'=>$model_card_item];
    }


    /**
     * 更改状态
     * @param string $order_sn
     * @param string $coverage
     * @return bool
     */
    public static function changeStatus($order_sn='',$coverage=''){

        $tj=[
            'pay_sn'=>$order_sn,
            'coverage_code'=>$coverage,
            'status'=>[0,1]
        ];
        $model=self::findOne($tj);
        if($model){
            $model->status =3;
            if($model->save()){
                return true;
            }
        }

        return false;
    }

	/**
	 *商家取消对卡券申请操作请求
	*/
	public  function cancelApply($carOrderPaybackObject)
	{
		$this->status = self::_CD_STATE_FAIL;
		$this->add_time = time();
		if($this->update(false,['status','add_time'])){

			$carOrderPaybackObject->num -=$this->number;
			$carOrderPaybackObject->total_price -=$this->number*$this->price;
			$carOrderPaybackObject->add_time = time();
			$carOrderPaybackObject->update(false,['num','total_price','add_time']);
			CardOrderItemLog::addLog(['order_id'=>$this->order_id,'content'=>'领取消#'.$this->coverage_code]);
			return true;
		}
		return false;
	}

}
