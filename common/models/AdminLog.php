<?php

namespace common\models;

use common\tool\Fun;
use Yii;

/**
 * This is the model class for table "{{%_admin_log}}".
 *
 * @property string $id
 * @property string $description
 * @property string $action
 * @property string $module
 * @property integer $recode_id
 * @property string $created_at
 * @property string $username
 * @property integer $uid
 */
class AdminLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_admin_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recode_id', 'uid'], 'integer'],
            [['created_at'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['action'], 'string', 'max' => 20],
            [['module', 'username'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'action' => 'Action',
            'module' => 'Module',
            'recode_id' => 'Recode ID',
            'created_at' => 'Created At',
            'username' => 'Username',
            'uid' => 'Uid',
        ];
    }

	public static function loginLog($admin)
	{
		$time = time();
		$client_ip = Fun::getClientIp();
		$admin->login_at = $time;
		$admin->login_ip = $client_ip;
		$admin->update(false,['login_at','login_ip']);
		$role = $admin->getRoleInfo();
		$log = new AdminLog();
		$log->attributes = [
			'description'=>"登陆角色【{$role[name]}】#IP:{$client_ip}#电话：{$admin[phone]}",
			'created_at'=>date('Y-m-d H:i:s',$time),
			'username'=>$admin->username,
			'uid'=>$admin->id
		];
		return $log->insert(false);
	}

	public static function LogOutLog()
	{
		$admin = Yii::$app->user->identity;
		$admin->login_at = 0;
		$admin->login_ip = '';
		$admin->update(false,['login_at','login_ip']);
		$role = $admin->getRoleInfo();
		$time = time();
		$client_ip = Fun::getClientIp();
		$log = new AdminLog();
		$log->attributes = [
			'description'=>"退出角色【{$role[name]}】#IP:{$client_ip}#电话：{$admin[phone]}",
			'created_at'=>date('Y-m-d H:i:s',$time),
			'username'=>$admin->username,
			'uid'=>$admin->id
		];
		return $log->insert(false);
	}

}
