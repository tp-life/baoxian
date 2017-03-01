<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;

/**
 * This is the model class for table "{{%_card_coupons_grant}}".
 *
 * @property string $id
 * @property string $card_number
 * @property string $card_secret
 * @property string $seller_id
 * @property integer $status
 * @property string $active_time
 * @property integer $ymd
 * @property integer $order_id
 * @property string $coverage_id
 * @property string $coverage_code
 * @property integer $type_id
 * @property integer $company_id
 * @property string $created
 */
class CardCouponsGrant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_card_coupons_grant}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_number', 'card_secret', 'seller_id'], 'required'],
            [['seller_id', 'status', 'active_time', 'ymd', 'order_id', 'coverage_id', 'type_id', 'company_id'], 'integer'],
            [['created'], 'safe'],
            [['card_number'], 'string', 'max' => 13],
            [['card_secret'], 'string', 'max' => 12],
            [['coverage_code'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_number' => 'Card Number',
            'card_secret' => 'Card Secret',
            'seller_id' => 'Seller ID',
            'status' => 'Status',
            'active_time' => 'Active Time',
            'ymd' => 'Ymd',
            'order_id' => 'Order ID',
            'coverage_id' => 'Coverage ID',
            'coverage_code' => 'Coverage Code',
            'type_id' => 'Type ID',
            'company_id' => 'Company ID',
            'created' => 'Created',
        ];
    }

	public function getCoverageInfo()
	{
		return $this->hasOne(InsuranceCoverage::className(),['id'=>'coverage_id'])->one();
	}

	//status` tinyint(4) unsigned DEFAULT '0' COMMENT '0默认 1 激活 2 失效 3 冻结（暂时不可用）',

	const __STATUS_DEFAULT = 0;//默认 未激活
	const __STATUS_ACTIVE = 1;//激活
	const __STATUS_FAIL = 2;//失效
	const __STATUS_FROZE = 3;// 冻结

	public static function statusData()
	{
		return [
			self::__STATUS_DEFAULT => '未激活',
			self::__STATUS_ACTIVE => '已激活',
			self::__STATUS_FAIL => '失效',
			self::__STATUS_FROZE => '冻结',

		];
	}
	public function getStatusText()
	{
		$t = self::statusData();
		return isset($t[$this->status]) ? $t[$this->status] : '';
	}


    public static function hasCardSecret($card_secret='')
    {
        return static::findOne(['card_secret'=>$card_secret]);
    }

    /**
     * 通过商家ID无分页获取所有类型的保险险种
     * seller_id 商家ID
     */
    public static function getCoverageCodeList($seller_id)
    {
        if(!$seller_id){
            return [];
        }
        return static::find()
            ->where(['seller_id'=>$seller_id,'status'=>0])
            ->groupBy('coverage_code')
            ->orderBy('coverage_id desc')
            ->select('coverage_id,coverage_code')
            ->all();
    }

    /**
     * 获取商家卡券数量
     * @param seller_id
     * @param status(0默认 1 激活 2 失效)
     */
    public static function getSellerCardCouponsNum($seller_id,$status = '')
    {
        if(!$seller_id){
            return '';
        }
        $map['seller_id'] = $seller_id;
        if(!empty($status) || $status === 0){
            $map['status'] = $status;
        }

        return static::find()
            ->where($map)
            ->count('id');
    }


    /**
     * 合并卡券
     * @param array $tj
     * @param array $data
     * @return bool
     * @throws \yii\db\Exception
     */
    public  static  function _merage($tj=[],$data=[]){
        $list = self::find()->where($tj)->asArray()->all();
        $to_seller_id = $data['to_seller_id'];
        $id_s = '';
        $transaction = Yii::$app->getDb()->beginTransaction();
        foreach($list as $v){
            $id_s .= $v['id'].',';
        }
        $id_s = trim($id_s,',');
        try{
            $sql2 = 'update '.self::tableName().' set seller_id = '.$to_seller_id .' where id in('.$id_s.')';
            $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
            $model_create = new CardGrantRelation();
            $ret3=$model_create ->createCard($list,$data);
            if($ret2 && $ret3){
                $transaction->commit();
                return true;
            }
            throw new Exception('error');
        }catch (Exception $e){
            $transaction->rollBack();
            return false;
        }
    }


    /**
     * 作废卡券
     */
    public static function cast($cards=[]){
        $sql2 = 'update '.self::tableName().' set status = 2 where id in('.join(',',$cards).')';
        $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
        return $ret2;
    }

    /**
     * 检查卡券是否允许退款
     */
    public static  function checkRefund($seller_id ='',Array $cards=[]){
        $data=[];
        try{
            if(!$seller_id || !$cards) throw new Exception('缺少必要参数');
            $condition =[
                'seller_id'=>$seller_id,
                'status'=>[self::__STATUS_DEFAULT,self::__STATUS_FROZE],
                'card_number'=>$cards
            ];
            $count=self::find()->where($condition)->asArray()->all();
            if(!$count){
                throw new Exception('退卡卡券异常，退卡失败');
            }
            if(count($count) != count($cards)){
                //已经激活或不可用的卡券
                $data['active']=[];
                foreach($count as $val){
                    if(in_array($val['status'],[self::__STATUS_ACTIVE,self::__STATUS_FAIL])){
                        $data['active'][]=$val;
                    }
                }
                $invalid=array_column($count,'card_number');
                $data['error'] = array_diff($cards,$invalid);
                throw new Exception('卡券 [ '.join(',', $data['error'].' ] 已经激活或状态不可用，请检查确认后重新退卡。'));
            }
            //退卡卡券险种
            $data['coverage']  = array_unique(array_column($count,'coverage_id'));

        }catch (Exception $e){
            return ['status'=>false,'msg'=>$e->getMessage(),'data'=>$data];
        }
        return ['status'=>true,'msg'=>'','data'=>$data];
    }

    /**
     * 获取已经激活与失效的卡券
     * @param string $seller_id
     * @param array $cards
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public static function getInvalid($seller_id ='',Array $cards=[]){
        if(!$seller_id || !$cards) return false;
        $condition =[
            'selller_id'=>$seller_id,
            'status'=>[self::__STATUS_ACTIVE,self::__STATUS_FAIL],
            'card_number'=>$cards
        ];
        return self::find()->where($condition)->asArray()->all();
    }

    /**
     * 获取卡券的总金额
     */
    public static function getCountPrice($cards=[]){
        $total=0;
        if(!$cards) return $total;
        $result=self::find()->select('coverage_id,count(coverage_id) as total')->where(['card_number'=>$cards])->groupBy('coverage_id')->asArray()->all();
        $coverage=array_column($result,'coverage_id');
        $cover_info = InsuranceCoverage::find()->where(['id'=>$coverage])->all();
        $temp=[];
        foreach($cover_info as $v){
            $temp[$v->id]=$v->wholesale_price;
        }
        foreach ($result as $val){
            if(isset($temp[$val['coverage_id']])){
                $total += $temp[$val['coverage_id']] * $val['total'];
            }
        }
        return $total;
    }

	/**
	 *获取商家 卡券支付的订单理赔统计情况
	*/
	public static function countCardRelationMaintenOrder($seller_id=0)
	{
		$query = new Query();
		$query->select('a.id')->from(['a'=>self::tableName(),'b'=>OrderMaintenance::tableName()])->where('a.order_id=b.order_id and a.seller_id='.intval($seller_id));
		return $query->count('a.id');
	}

	/**
     * 更改卡券状态
     */
	public static function changeStatus($cards=[],$status=''){
        if(!$cards || !in_array($status,[self::__STATUS_DEFAULT,self::__STATUS_ACTIVE,self::__STATUS_FAIL,self::__STATUS_FROZE])){
            return false;
        }
        if(is_string($cards)){
            $cards = explode(',',$cards);
        }
        $cards = array_map(function($card){
            return "'".$card."'";
        },$cards);
        $sql2 = 'update '.CardCouponsGrant::tableName().' set status = '.$status .' where card_number in('.join(',',$cards).')';
        $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
        return $ret2;
    }


    /**
     * 退卡处理
     */
    public static function changeSellerStatus($cards=[],$status='',$seller_id = 1){
        if(!$cards || !in_array($status,[self::__STATUS_DEFAULT,self::__STATUS_ACTIVE,self::__STATUS_FAIL,self::__STATUS_FROZE])){
            return false;
        }
        if(is_string($cards)){
            $cards = explode(',',$cards);
        }
        if(!$cards){
            return false;
        }
        $cards = array_map(function($card){
            return "'".$card."'";
        },$cards);
        $sql2 = 'update '.CardCouponsGrant::tableName().' set status = '.$status .',seller_id = '.$seller_id.' where card_number in('.join(',',$cards).')';
        $ret2 = Yii::$app->getDb()->createCommand($sql2)->execute();
        return $ret2;
    }

    public static function getInfoByOrder($order_id){
        if(!$order_id){
            return false;
        }
        $tb=self::tableName();
         return self::find()->select('s.*,'.$tb.'.*')->innerJoin(['s'=>Seller::tableName()],'s.seller_id = '.$tb.'.seller_id')->where(['order_id'=>$order_id])->asArray()->one();
    }
}
