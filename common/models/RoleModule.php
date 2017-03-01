<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_role_module}}".
 *
 * @property integer $id
 * @property string $module
 * @property string $name
 * @property integer $is_effect
 */
class RoleModule extends \yii\db\ActiveRecord
{

	//场景定义
	const ADMIN_CREATE = 'admin_create_';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_role_module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module', 'name', 'is_effect'], 'required'],
            [['is_effect'], 'integer'],
			['is_effect', 'default', 'value' => 1],
            [['module'], 'string','min'=>2, 'max' => 30],
            [['name'], 'string', 'min'=>2,'max' => 15],
			['module','verfiyModule']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module' => 'Module',
            'name' => 'Name',
            'is_effect' => 'Is Effect',
        ];
    }

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::ADMIN_CREATE] = ['module', 'name', 'is_effect'];
		return $scenarios;
	}

	public function verfiyModule($attribute, $params)
	{
		if(self::find()->where('id<>:id and module=:module',[':id'=>$this->id,':module'=>$this->module])->one()){
			$this->addError($attribute,'模块已经存在');
			return false;
		}
		return true;
	}

	/**
	 * 关联模块方法
	*/
	public function getModuleActions()
	{
		return $this->hasMany(RoleAction::className(),['module_id'=>'id']);
	}

}
