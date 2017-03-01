<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%_member}}".
 *
 * @property integer $member_id
 * @property string $phone
 * @property string $name
 * @property string $avatar
 * @property string $passwd
 * @property string $paypwd
 * @property integer $state
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 1;

	const __PWD_SALT = 'baoxian';
	const __DEFAULT_PASS = '123456';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'name', 'passwd'], 'required'],
            [['state'], 'integer'],
            [['phone'], 'string', 'max' => 11],
			['phone', 'match', 'pattern' => '/^1[34578]{1}\d{9}$/', 'message' => '请填写11位有效电话号码'],
			[['phone'], 'unique','message'=>'该手机号已注册'],
            [['name', 'avatar'], 'string', 'max' => 50],
            [['passwd', 'paypwd'], 'string', 'max' => 32],
				[['name'], 'unique','message'=>'该用户已经存在'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'phone' => 'Phone',
            'name' => ' 用户名',
            'avatar' => 'Avatar',
            'passwd' => '用户密码',
            'paypwd' => 'Paypwd',
            'state' => 'State',
        ];
    }
	public static function findByPhone($phone)
	{
		return static::findOne(['phone' => $phone, 'state' => self::STATUS_ACTIVE]);
	}
	public static function findIdentity($id)
	{
		return static::findOne(['member_id' => $id, 'state' => self::STATUS_ACTIVE]);
	}

	public static function findIdentityByAccessToken($token, $type = null)
	{
		throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}

	public function getId(){
		return $this->getPrimaryKey();
	}

	public function getAuthKey()
	{
		return null;
	}

	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}
	public function validatePassword($password)
	{
		return $this->passwd == $this->createPassword($password);
		//return Yii::$app->security->validatePassword($password, $this->password);
	}

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->passwd = $this->createPassword($password);
	}

	public function createPassword($password)
	{
		return md5(self::__PWD_SALT.strtolower($password));
	}

	/**
	 * Generates "remember me" authentication key
	 */
	public function generateAuthKey()
	{
	}

	/**
	 * Generates new password reset token
	 */
	public function generatePasswordResetToken()
	{
	}

	/**
	 * Removes password reset token
	 */
	public function removePasswordResetToken()
	{

	}
	public function getSellerInfo($where = array())
	{
		return $this->hasOne(Seller::className(),['member_id'=>'member_id'])->where($where)->one();
	}
}
