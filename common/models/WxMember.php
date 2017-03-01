<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_wx_member}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $openid
 * @property string $nickname
 * @property string $city
 * @property string $province
 * @property string $headimgurl
 * @property string $token
 * @property string $client_type
 * @property string $datetime
 */
class WxMember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_wx_member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'integer'],
            [['datetime'], 'safe'],
            [['openid','unionid', 'nickname', 'token'], 'string', 'max' => 50],
            [['city', 'province'], 'string', 'max' => 20],
            [['headimgurl'], 'string', 'max' => 300],
            [['client_type'], 'string', 'max' => 10],
            [['openid'], 'unique'],
            [['token'], 'unique'],
            [['member_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户ID',
            'openid' => '微信openid',
            'nickname' => '微信昵称',
            'city' => '城市',
            'province' => '省份',
            'headimgurl' => '头像地址',
            'token' => 'Token',
            'client_type' => '客户端类型 android ios m weixin',
            'datetime' => '登录时间',
			'unionid' => 'unionid'
        ];
    }

}
