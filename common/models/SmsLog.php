<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_sms_log}}".
 *
 * @property string $log_id
 * @property string $type
 * @property string $phone
 * @property string $captcha
 * @property string $content
 * @property integer $member_id
 * @property string $agent
 * @property string $ip
 * @property string $send_time
 */
class SmsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_sms_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'captcha', 'content', 'agent'], 'required'],
            [['content', 'agent'], 'string'],
            [['send_time','captcha'], 'safe'],
            [['type'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 11],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'type' => '消息类型',
            'phone' => 'Phone',
            'captcha' => '动态验证#',
            'content' => 'Content',
            'agent' => '代理',
            'ip' => 'ip 记录',
            'send_time' => '消息加入队列时间 ',
        ];
    }

	/**
	 * @param $phone 手机号
	 * @param $type 类型
	 * 获取最后一次发送验证码的时间
	 */
	public static function getLastSendTime($phone, $type)
	{
		$info =  self::find()->where(['phone'=>$phone, 'type'=>$type])->select('send_time')->orderBy('send_time DESC')->asArray()->one();
		return isset($info['send_time']) ? $info['send_time'] : 0;
	}
	public static function gainNumberCode($phone,$type)
	{
		$now = time();
		$date = date("Y-m-d 00:00:01",$now);
		return self::find()->where(['phone'=>$phone,'type'=>$type])->andWhere("send_time>='$date'")->count('log_id');
	}


}
