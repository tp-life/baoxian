<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_conf}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property integer $group_id
 * @property string $china_name
 */
class Conf extends \yii\db\ActiveRecord
{

	const CONF_SYSTEM = 1;
	const CONF_PROJECT = 2;
	const CONF_OTHER = 3;

	public static $confType = array(
		self::CONF_SYSTEM => '系统配置',
		self::CONF_PROJECT => '项目配置',
		self::CONF_OTHER => '其他配置'
	);

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_conf}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'china_name'], 'required'],
            [['group_id'], 'integer'],
            [['name', 'value', 'china_name'], 'string', 'max' => 150],
            [['name'], 'unique','message'=>'Key已经存在'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'group_id' => 'Group ID',
            'china_name' => 'China Name',
        ];
    }

	public function getGroupTypeTxt()
	{
		return self::$confType[$this->group_id];
	}

	public static function getValue($key = null)
	{
		if(empty($key)){
			return false;
		}
		if ($value = Yii::$app->cache->get($key)) {
			return $value;
		}
		$value = self::find()->select('value')->where(['name' => $key])->scalar();
		if ($value) {
			Yii::$app->cache->set($key, $value);
		}
		return $value;

	}

}
