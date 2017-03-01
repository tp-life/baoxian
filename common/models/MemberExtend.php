<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_member_extend}}".
 *
 * @property string $id
 * @property string $member_id
 * @property integer $sex
 * @property string $province_id
 * @property string $city_id
 * @property string $area_id
 * @property string $area_info
 * @property string $detail_address
 * @property string $concat
 * @property string $concat_tel
 * @property integer $register_time
 * @property integer $login_num
 * @property string $last_login_ip
 * @property integer $last_login_time
 */
class MemberExtend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_member_extend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id', 'sex', 'province_id', 'city_id', 'area_id', 'register_time', 'login_num', 'last_login_time'], 'integer'],
            [['area_info', 'detail_address'], 'string', 'max' => 100],
            [['concat', 'concat_tel'], 'string', 'max' => 50],
            [['last_login_ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'sex' => 'Sex',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'area_id' => 'Area ID',
            'area_info' => 'Area Info',
            'detail_address' => 'Detail Address',
            'concat' => 'Concat',
            'concat_tel' => 'Concat Tel',
            'register_time' => 'Register Time',
            'login_num' => 'Login Num',
            'last_login_ip' => 'Last Login Ip',
            'last_login_time' => 'Last Login Time',
        ];
    }
}
