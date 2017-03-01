<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_role_access}}".
 *
 * @property string $id
 * @property integer $role_id
 * @property integer $action_id
 * @property integer $module_id
 */
class RoleAccess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_role_access}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'action_id', 'module_id'], 'required'],
            [['role_id', 'action_id', 'module_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'action_id' => 'Action ID',
            'module_id' => 'Module ID',
        ];
    }

	public function getRoleInfo()
	{
		return $this->hasOne(Role::className(), ['id' => 'role_id'])->one();
	}

	public function getActionInfo()
	{
		return $this->hasOne(RoleAction::className(), ['id' => 'action_id'])->one();
	}

	public function getModuleInfo()
	{
		return $this->hasOne(RoleModule::className(), ['id' => 'module_id'])->one();
	}

}
