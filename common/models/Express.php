<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_express}}".
 *
 * @property integer $id
 * @property string $e_name
 * @property string $e_state
 * @property string $e_code
 * @property string $e_letter
 * @property string $e_order
 * @property string $e_url
 * @property integer $e_zt_state
 */
class Express extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_express}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['e_name', 'e_code', 'e_letter', 'e_url'], 'required'],
            [['e_state', 'e_order'], 'string'],
            [['e_zt_state'], 'integer'],
            [['e_name', 'e_code'], 'string', 'max' => 50],
            [['e_letter'], 'string', 'max' => 1],
            [['e_url'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '索引ID',
            'e_name' => '公司名称',
            'e_state' => '状态',
            'e_code' => '编号',
            'e_letter' => '首字母',
            'e_order' => '1常用2不常用',
            'e_url' => '公司网址',
            'e_zt_state' => '是否支持服务站配送0否1是',
        ];
    }


	public static function getAllExpress()
	{
		$cache_key = 'express_leo_abc';
		if($data = Yii::$app->cache->get($cache_key)){
			return $data;
		}
		$data =  self::find()->orderBy("e_order ASC, id ASC")->asArray()->all();
		if($data){
			Yii::$app->cache->set($cache_key,$data,3600);
		}
		return $data;
	}

}
