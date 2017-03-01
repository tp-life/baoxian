<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_seller}}".
 *
 * @property string $seller_id
 * @property string $seller_name
 * @property string $member_id
 * @property integer $is_insurance
 * @property integer $is_repair
 * @property string $province_id
 * @property string $city_id
 * @property string $area_id
 * @property string $area_info
 * @property string $detail_address
 * @property string $concat
 * @property string $concat_tel
 * @property integer $status
 * @property integer $add_time
 */
class Seller extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_seller}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seller_name', 'member_id'], 'required'],
            [['member_id','pid', 'is_insurance', 'is_repair', 'province_id', 'city_id', 'area_id', 'status', 'add_time'], 'integer'],
            [['seller_name', 'concat'], 'string', 'max' => 50],
            [['area_info', 'detail_address'], 'string', 'max' => 100],
            [['concat_tel'], 'string', 'max' => 11],
        ];
    }

    public function getBank(){
        return $this->hasMany(Bank::className(), ['member_id' => 'member_id'])->where(['status'=>1]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'seller_id' => 'Seller ID',
            'seller_name' => 'Seller Name',
            'member_id' => 'Member ID',
            'is_insurance' => 'Is Insurance',
            'is_repair' => 'Is Repair',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'area_id' => 'Area ID',
            'area_info' => 'Area Info',
            'detail_address' => 'Detail Address',
            'concat' => 'Concat',
            'concat_tel' => 'Concat Tel',
            'status' => 'Status',
            'add_time' => 'Add Time',
			'pid' => 'Pid',
        ];
    }

	//乐换新商家账号 id 数组
	static $lehuanxin = [1];

	public function getIsLehuanxin()
	{
		return in_array($this->seller_id,self::$lehuanxin);
	}

	/**
	 * 判断是否是 二级商家
	*/
	public function getIsRankTwo()
	{
		return $this->pid ? true : false;
	}

    /**
     * 无分页获取保险商家或者维修商家
     * type(insurance-保险商户，repair-维修商户)
     */
    public static function getSellerList($type)
    {
        if(!in_array($type,['insurance','repair'])){
            return [];
        }

        $newType = 'is_'.$type;

        return static::findAll([$newType=>1,'status'=>1]);
    }

    /**
     * 根据商家ID获取商家详情
     * seller_id 商家ID
     */
    public static function getSellerInfo($seller_id)
    {
        if(!$seller_id){
            return [];
        }
		$cache = Yii::$app->cache;
		$cache_key = 'seller_'.$seller_id;
		if($row = $cache->get($cache_key)){
			return $row;
		}
		$row = self::find()->where(['seller_id'=>$seller_id])->one();
		if($row){
			$cache->set($cache_key,$row,3600);
		}
		return $row;
    }

    public function getStatusTxt()
    {
        return $this->status ? '合作中' : '已终止';
    }

    /**
     * 更具用户ID获取商家信息
     * @param string $member_id
     * @return array|bool|null|\yii\db\ActiveRecord
     */
    public static function getSeller($member_id=''){
        if(!$member_id) return false;
        return static::find()->where(['member_id'=>$member_id])->one();
    }

    /**
     * 返回以商家ID作为键的数组信息
     * @param array $tj
     * @return array
     */
    public static  function  getIdKeySeller($tj=[]){
        $result=self::find()->where($tj)->asArray()->all();
        $temp=[];
        foreach($result as $val){
            $temp[$val['seller_id']]=$val;
        }
        return $temp;
    }
}
