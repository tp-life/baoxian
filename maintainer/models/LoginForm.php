<?php
namespace maintainer\models;
use common\models\Admin;
use common\models\Member;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误?');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            if(Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0)){
				return true;
			}
        }
		return false;
    }

	/**
	 * 维修商家商家登录
	**/
	public function loginSeller()
	{
		if ($this->validate()) {
			$user = $this->getUser();
			$seller = $user->getSellerInfo();
			if(empty($seller)){
				$this->addError('username','无商家登录权限');
			}
			if($seller && Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0)){
				return true;
			}
		}
		return false;
	}

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Member::findByPhone($this->username);
        }

        return $this->_user;
    }
}