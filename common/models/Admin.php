<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%_admin}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property string $email
 * @property integer $is_system
 * @property integer $role_id
 * @property integer $login_at
 * @property string $login_ip
 */
class Admin extends ActiveRecord implements IdentityInterface
{
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_admin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'phone', 'role_id'], 'required'],
            [['is_system', 'role_id', 'login_at'], 'integer'],
            [['username', 'login_ip'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 11],
			['phone', 'match', 'pattern' => '/^1[34578]{1}\d{9}$/', 'message' => '请填写11位有效电话号码'],
            [['email'], 'string', 'max' => 150],
			[['username'], 'verfiyUsername'],
            [['username'], 'unique','message'=>'用户名已经存在'],
			['role_id','exist','targetClass'=>'common\models\Role','targetAttribute'=>'id','message'=>'角色不存在'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'username' => '用户名',
            'password' => '密码',
            'phone' => '电话',
            'email' => '邮箱',
            'is_system' => '是否系统管理',
            'role_id' => '角色',
            'login_at' => 'Login At',
            'login_ip' => 'Login Ip',
        ];
    }

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername($username)
	{
		return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
	}
	/**
	 * Finds an identity by the given ID.
	 * @param string|integer $id the ID to be looked for
	 * @return IdentityInterface the identity object that matches the given ID.
	 * Null should be returned if such an identity cannot be found
	 * or the identity is not in an active state (disabled, deleted, etc.)
	 */
	public static function findIdentity($id)
	{
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}


	/**
	 * Finds an identity by the given token.
	 * @param mixed $token the token to be looked for
	 * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
	 * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
	 * @return IdentityInterface the identity object that matches the given token.
	 * Null should be returned if such an identity cannot be found
	 * or the identity is not in an active state (disabled, deleted, etc.)
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}

	/**
	 * Returns an ID that can uniquely identify a user identity.
	 * @return string|integer an ID that uniquely identifies a user identity.
	 */
	public function getId(){
		return $this->getPrimaryKey();
	}

	/**
	 * Returns a key that can be used to check the validity of a given identity ID.
	 *
	 * The key should be unique for each individual user, and should be persistent
	 * so that it can be used to check the validity of the user identity.
	 *
	 * The space of such keys should be big enough to defeat potential identity attacks.
	 *
	 * This is required if [[User::enableAutoLogin]] is enabled.
	 * @return string a key that is used to check the validity of a given identity ID.
	 * @see validateAuthKey()
	 */
	public function getAuthKey()
	{
		return null;
	}

	/**
	 * Validates the given auth key.
	 *
	 * This is required if [[User::enableAutoLogin]] is enabled.
	 * @param string $authKey the given auth key
	 * @return boolean whether the given auth key is valid.
	 * @see getAuthKey()
	 */
	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}

	/**
	 * Validates password
	 *
	 * @param string $password password to validate
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return $this->password == $this->createPassword($password);
		//return Yii::$app->security->validatePassword($password, $this->password);
	}

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $this->createPassword($password);
	}

	public function createPassword($password)
	{
		return md5('$%&^'.strtolower($password));
	}

	/**
	 * Generates "remember me" authentication key
	 */
	public function generateAuthKey()
	{
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Generates new password reset token
	 */
	public function generatePasswordResetToken()
	{
		$this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
	}

	/**
	 * Removes password reset token
	 */
	public function removePasswordResetToken()
	{
		$this->password_reset_token = null;
	}

	public function getRoleInfo()
	{
		return $this->hasOne(Role::className(),['id'=>'role_id'])->one();
	}

	public function getIsSystemRole()
	{
		$role =  $this->getRoleInfo();
		if($role['is_system']){
			return true;
		}
		return false;
	}

	public function verfiyUsername($attribute, $params)
	{
		if(self::find()->where('id<>:id and username=:username',[':id'=>$this->id,':username'=>$this->username])->one()){
			$this->addError($attribute,'用户已经存在');
			return false;
		}
		return true;
	}

}
