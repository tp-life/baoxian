<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%_maintenance_offer}}".
 *
 * @property integer $id
 * @property integer $seller_id
 * @property integer $offer_id
 * @property integer $status
 * @property string $update_time
 */
class MaintenanceOffer extends \yii\db\ActiveRecord
{
	const __STATUS_STOP = 0;//暂停
	const __STATUS_START = 1;//启用


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_maintenance_offer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'seller_id', 'offer_id', 'status'], 'integer'],
            [['update_time'], 'safe'],
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
            'offer_id' => 'Offer ID',
            'status' => 'Status',
            'update_time' => 'Update Time',
        ];
    }

	/**
	 * 根据 商家  品牌  型号 或者报价列表
	*/
	public static function getSellerOffer($seller_id,$brand_id,$model_id,$color_id=0)
	{
		$query = new Query();
		$query->select('a.offer_id,a.name,a.inner_screen,a.outer_screen,a.commission')->from(['a'=>BrandOffer::tableName(),'b'=>MaintenanceOffer::tableName()])->where('a.offer_id=b.offer_id');
		$query->andWhere('a.status=1');
		$query->andWhere('b.status=1');
		$query->andWhere('a.brand_id=:brand_id',[':brand_id'=>$brand_id]);
		$query->andWhere('a.model_id=:model_id',[':model_id'=>$model_id]);
		$query->andWhere('b.seller_id=:seller_id',[':seller_id'=>$seller_id]);
		$color_id && $query->andWhere('a.color_id=:color_id',[':color_id'=>$color_id]);
		return $query->all();
	}

	/**
	 * 验证 商家 对应的机型报价 是否存在
	 * **/
	public static function checkSellerOffer($seller_id,$brand_id,$model_id,$offer_id)
	{
		$query = new Query();
		$query->select('a.offer_id,a.name,a.inner_screen,a.outer_screen,a.commission')->from(['a'=>BrandOffer::tableName(),'b'=>MaintenanceOffer::tableName()])->where('a.offer_id=b.offer_id');
		$query->andWhere('a.status=1');
		$query->andWhere('b.status=1');
		$query->andWhere('a.brand_id=:brand_id',[':brand_id'=>$brand_id]);
		$query->andWhere('a.model_id=:model_id',[':model_id'=>$model_id]);
		$query->andWhere('b.seller_id=:seller_id',[':seller_id'=>$seller_id]);
		$query->andWhere('a.offer_id=:offer_id',[':offer_id'=>$offer_id]);
		return $query->one();
	}

	public static function updateChangeLog($update,$where)
	{
		return self::updateAll($update,$where);
	}

	/**
	 * 根据 商家 获取报价变动日志
	*/
	public static function getChangeLog($seller_id)
	{
		$query = new Query();
		$query->select('a.offer_id,a.handle_type,a.content,a.update_time,b.id')->from(['a'=>BrandOfferUpdateLog::tableName(),'b'=>MaintenanceOffer::tableName()])->where('a.id=b.offer_change_log_id');
		$query->andWhere('b.status='.self::__STATUS_STOP);
		$query->andWhere('b.seller_id=:seller_id',[':seller_id'=>$seller_id]);
		$query->orderBy('a.update_time DESC');
		return $query->all();
	}

}
