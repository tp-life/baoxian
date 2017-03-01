<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_bank}}".
 *
 * @property string $bank_id
 * @property string $member_id
 * @property string $brank_name
 * @property string $brank_account
 * @property string $account_holder
 * @property integer $sort
 * @property integer $status
 * @property integer $is_default
 * @property integer $add_time
 */
class Bank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_bank}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'sort', 'status', 'is_default', 'add_time'], 'integer'],
            [['add_time'], 'required'],
            [['brank_name'], 'string', 'max' => 50],
            [['brank_account'], 'string', 'max' => 20],
            [['account_holder'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bank_id' => 'Bank ID',
            'member_id' => 'Member ID',
            'brank_name' => 'Brank Name',
            'brank_account' => 'Brank Account',
            'account_holder' => 'Account Holder',
            'sort' => 'Sort',
            'status' => 'Status',
            'is_default' => 'Is Default',
            'add_time' => 'Add Time',
        ];
    }
}
