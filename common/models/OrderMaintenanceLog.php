<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_order_maintenance_log}}".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $m_order_id
 * @property string $mark
 * @property string $uid
 * @property string $name
 * @property string $add_time
 */
class OrderMaintenanceLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_order_maintenance_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'm_order_id'], 'required'],
            [['order_id', 'm_order_id', 'uid'], 'integer'],
            [['add_time','is_show'], 'safe'],
            [['mark', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'm_order_id' => 'M Order ID',
            'mark' => 'Mark',
            'uid' => 'Uid',
            'name' => 'Name',
            'add_time' => 'Add Time',
			'is_show'=>'Is Show'
        ];
    }

	/**
	 *@param $order =>OrderMaintenance
	 *@param $order =>OrderMaintenanceService
	 *@param $mark 备注信息
	*/
	public static function addLog($order,$mark,$is_show=1)
	{
		$log = new OrderMaintenanceLog();
		$log->order_id = $order['order_id'];
		if($order instanceof OrderMaintenance){
			$log->m_order_id = $order['id'];
		}elseif($order instanceof OrderMaintenanceService){
			$log->m_order_id = $order['m_order_id'];
		}
		$log->mark = $mark;
		$log->is_show = $is_show;
		if(!Yii::$app->user->isGuest){
			if( Yii::$app->user->identity instanceof Admin){
				$log->uid = Yii::$app->user->identity->id;
				$log->name = '[平台]'.Yii::$app->user->identity->username;
			}elseif(Yii::$app->user->identity instanceof Member){
				$log->uid = Yii::$app->user->identity->member_id;
				$log->name = '[商家]'.Yii::$app->user->identity->name.'['.Yii::$app->user->identity->phone.']';
			}
		}
		$log->add_time = date("Y-m-d H:i:s",time());
		return $log->save(false);

	}

}
