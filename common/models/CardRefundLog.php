<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fj_card_refund_log".
 *
 * @property integer $id
 * @property integer $refund_id
 * @property string $content
 * @property integer $uid
 * @property string $name
 * @property string $update_time
 */
class CardRefundLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fj_card_refund_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['refund_id', 'content', 'uid', 'name'], 'required'],
            [['refund_id', 'uid'], 'integer'],
            [['update_time'], 'safe'],
            [['content'], 'string', 'max' => 200],
            [['name'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refund_id' => 'Refund ID',
            'content' => 'Content',
            'uid' => 'Uid',
            'name' => 'Name',
            'update_time' => 'Update Time',
        ];
    }

	public static function addLog($refund_id, $content)
	{
		$log = new CardRefundLog();
		if (Yii::$app->user->identity instanceof Admin) {
			$log->uid = Yii::$app->user->identity->id;
			$log->name = '[平台]' . Yii::$app->user->identity->username;
		} elseif (Yii::$app->user->identity instanceof Member) {
			$log->uid = Yii::$app->user->identity->member_id;
			$log->name = '[商家]' . Yii::$app->user->identity->name . '[' . Yii::$app->user->identity->phone . ']';
		}
		$log->refund_id = $refund_id;
		$log->content = $content;
		$log->update_time = date('Y-m-d H:i:s');
		return $log->insert(false);
	}

}
