<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_role}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_system
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_system'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'is_system' => 'Is System',
        ];
    }

	public function getRoleAccess()
	{
		return $this->hasMany(RoleAccess::className(),['role_id'=>'id'])->all();
	}

	public static function getAll()
	{
		return self::find()->orderBy('id DESC')->asArray()->all();
	}
}
