<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_brand_offer}}".
 *
 * @property integer $offer_id
 * @property integer $m_id
 * @property integer $brand_id
 * @property integer $model_id
 * @property integer $color_id
 * @property string $name
 * @property string $inner_screen
 * @property string $outer_screen
 * @property integer $commission
 * @property string $update_time
 */
class BrandOffer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_brand_offer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'brand_id', 'model_id'], 'required'],
            [[ 'brand_id', 'model_id', 'color_id', 'commission'], 'integer'],
			['commission','default','value'=>5],
            [['inner_screen', 'outer_screen'], 'number'],
            [['update_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'offer_id' => 'Offer ID',
            'brand_id' => 'Brand ID',
            'model_id' => 'Model ID',
            'color_id' => 'Color ID',
            'name' => 'Name',
            'inner_screen' => 'Inner Screen',
            'outer_screen' => 'Outer Screen',
            'commission' => 'Commission',
            'update_time' => 'Update Time',
        ];
    }

}
