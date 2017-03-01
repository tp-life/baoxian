<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_role_nav}}".
 *
 * @property string $id
 * @property string $name
 * @property integer $nav_id
 * @property string $icon
 * @property integer $is_delete
 * @property integer $is_effect
 * @property integer $sort
 */
class RoleNav extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_role_nav}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'nav_id', 'is_effect', 'sort'], 'required'],
            [['nav_id', 'is_effect', 'sort'], 'integer'],
            [['name', 'icon'], 'string', 'max' => 30],
			['is_effect', 'default', 'value' => 1],
			['sort', 'default', 'value' => 99],
			['nav_id','exist','targetClass'=>'common\models\RoleNavGroup','targetAttribute'=>'id','filter'=>['is_effect'=>1],'message'=>'菜单栏目未启用'],
			['name','verfiyNav']
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
            'nav_id' => 'Nav ID',
            'icon' => 'Icon',
            'is_effect' => 'Is Effect',
            'sort' => 'Sort',
        ];
    }
	public function verfiyNav($attribute, $params)
	{
		if(self::find()->where('id<>:id and nav_id=:nav_id and name=:name',[':id'=>$this->id,':nav_id'=>$this->nav_id,':name'=>$this->name])->one()){
			$this->addError($attribute,'栏目下子菜单已经存在');
			return false;
		}
		return true;
	}
	/**
	 * 关联方法组
	 */
	public function getNavActions()
	{
		return $this->hasMany(RoleAction::className(),['group_id'=>'id'])->all();
	}
	public static function getAll()
	{
		return self::find()->where(['is_effect'=>1])->orderBy('id DESC')->asArray()->all();
	}
}
