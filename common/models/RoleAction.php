<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_role_action}}".
 *
 * @property string $id
 * @property string $action
 * @property string $name
 * @property integer $is_effect
 * @property string $group_id
 * @property string $module_id
 */
class RoleAction extends \yii\db\ActiveRecord
{
	//场景定义
	const ADMIN_CREATE = 'admin_create_';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_role_action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action', 'name', 'module_id'], 'required'],
            [['group_id', 'module_id'], 'integer'],
            [['action', 'name'], 'string', 'max' => 30],
			['group_id','exist','targetClass'=>'common\models\RoleNav','targetAttribute'=>'id','filter'=>['is_effect'=>1],'message'=>'菜单未生效'],
			['module_id','exist','targetClass'=>'common\models\RoleModule','targetAttribute'=>'id','filter'=>['is_effect'=>1],'message'=>'模块未启用'],
			['action','verfiyModulefun']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => 'Action',
            'name' => 'Name',
            'group_id' => 'Group ID',
            'module_id' => 'Module ID',
        ];
    }
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::ADMIN_CREATE] = ['action', 'name', 'group_id','module_id'];
		return $scenarios;
	}

	public function verfiyModulefun($attribute, $params)
	{
		if(self::find()->where('id<>:id and module_id=:module_id and action=:action',[':id'=>$this->id,':module_id'=>$this->module_id,':action'=>$this->action])->one()){
			$this->addError($attribute,'模块下方法已经存在');
			return false;
		}
		return true;
	}

	/**
	 * 关联模块
	 */
	public function getModuleObject()
	{
		return $this->hasOne(RoleModule::className(),['id'=>'module_id']);
	}
}
