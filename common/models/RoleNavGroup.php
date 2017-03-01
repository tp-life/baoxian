<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_role_nav_group}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $icons
 * @property integer $is_effect
 * @property integer $sort
 */
class RoleNavGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_role_nav_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'is_effect', 'sort'], 'required'],
            [['is_effect', 'sort'], 'integer'],
            [['name', 'icons'], 'string', 'max' => 30],
			['sort', 'default', 'value' => 99],
			[['name'], 'unique','message'=>'导航菜单已经存在']
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
            'icons' => 'Icons',
            'is_effect' => 'Is Effect',
            'sort' => 'Sort',
        ];
    }

	public static function getAll()
	{
		return self::find()->where(['is_effect'=>1])->orderBy('id DESC')->asArray()->all();
	}
	/**
	 * 关联子菜单
	 */
	public function getMenuItems()
	{
		return $this->hasMany(RoleNav::className(),['nav_id'=>'id'])->where(['is_effect'=>1])->orderBy('sort ASC')->all();
	}

}
