<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_area}}".
 *
 * @property integer $area_id
 * @property string $area_name
 * @property integer $area_parent_id
 * @property integer $area_sort
 * @property integer $area_deep
 * @property string $area_region
 * @property integer $is_delivery
 * @property integer $is_delivery_old_phone
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_area}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_name'], 'required'],
            [['area_parent_id', 'area_sort', 'area_deep', 'is_delivery', 'is_delivery_old_phone'], 'integer'],
            [['area_name'], 'string', 'max' => 50],
            [['area_region'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => 'Area ID',
            'area_name' => 'Area Name',
            'area_parent_id' => 'Area Parent ID',
            'area_sort' => 'Area Sort',
            'area_deep' => 'Area Deep',
            'area_region' => 'Area Region',
            'is_delivery' => 'Is Delivery',
            'is_delivery_old_phone' => 'Is Delivery Old Phone',
        ];
    }

    public static function getInfo($area_id=0,$col='')
	{       $cache_key = 'area_id'.$area_id;
	        $cache = Yii::$app->cache;
	        if($area_id && $row = $cache->get($cache_key)){
	        	if($col && isset($row[$col])){
	        		return $row[$col];
				}
	        	return $row;
			}
			$row = self::find()->where(['area_id'=>$area_id])->asArray()->one();
			if($row){
				$cache->set($cache_key,$row,3600);
			}
			if($col && isset($row[$col])){
				return $row[$col];
			}
			return $row;
	}
}
