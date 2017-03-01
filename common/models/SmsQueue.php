<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_sms_queue}}".
 *
 * @property string $id
 * @property string $type
 * @property string $phone
 * @property string $captcha
 * @property string $content
 * @property integer $member_id
 * @property string $ip
 * @property string $agent
 * @property string $send_time
 */
class SmsQueue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_sms_queue}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'content'], 'required'],
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
            'id' => 'ID',
            'type' => '消息类型',
            'phone' => 'Phone',
            'captcha' => '动态验证',
            'content' => 'Content',
            'ip' => 'ip 记录',
            'agent' => 'Agent',
            'send_time' => '消息加入队列时间 ',
        ];
    }


}
