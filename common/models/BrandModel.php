<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fj_brand_model".
 *
 * @property integer $id
 * @property string $model_name
 * @property integer $parent_id
 * @property string $first_word
 * @property integer $sort
 * @property integer $depth
 * @property integer $g_id
 * @property string $jintai_sn
 */
class BrandModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_brand_model}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort', 'depth', 'g_id'], 'integer'],
            [['model_name', 'jintai_sn'], 'string', 'max' => 50],
            [['first_word'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_name' => 'Model Name',
            'parent_id' => 'Parent ID',
            'first_word' => 'First Word',
            'sort' => 'Sort',
            'depth' => 'Depth',
            'g_id' => 'G ID',
            'jintai_sn' => 'Jintai Sn',
        ];
    }

	public function getBrand($id=''){
		 return $this->findOne(['id'=>$id]);
	}

    /**
     * 获取评牌型号
     * @param int $pid
     * @param array $tj
     * @param string $order_by
     * @param string $limt
     * @return mixed
     */
	public static function getPrentBrand($pid = 0,$field='*',$tj=[],$order_by='sort asc , id asc',$limt=''){
        return self::find()->select($field)->where(['parent_id'=>$pid])->andWhere($tj)->orderBy($order_by)->limit($limt)->asArray()->all();
    }
	/**
	 *获取品牌信息
	 */
	public static function getInfo($id)
	{
		if(!$id){
			return [];
		}
		$cache = Yii::$app->cache;
		$cache_key = 'brand_'.$id;
		if($row = $cache->get($cache_key)){
			return $row;
		}
		$row = self::find()->where(['id'=>$id])->one();
		if($row){
			$cache->set($cache_key,$row,3600);
		}
		return $row;
	}


}
