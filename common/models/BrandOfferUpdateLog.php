<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_brand_offer_update_log}}".
 *
 * @property string $id
 * @property string $offer_id
 * @property string $content
 * @property integer $uid
 * @property string $name
 * @property string $update_time
 */
class BrandOfferUpdateLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_brand_offer_update_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'content', 'uid', 'name'], 'required'],
            [['offer_id', 'uid'], 'integer'],
            [['update_time','handle_type'], 'safe'],
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
            'offer_id' => 'Offer ID',
            'content' => '价格异动记录  ',
            'uid' => 'Uid',
            'name' => 'Name',
            'update_time' => '每次插入或者更新',
			'handle_type'=>'类型'
        ];
    }

	const __HD__UPDATE = 0;
	const __HD__STOP = 1;

	public static function getHandleType($key = '')
	{
		$t = [
			self::__HD__UPDATE => '报价变动',
			self::__HD__STOP => '报价暂停'
		];
		return isset($t[$key]) ? $t[$key] : $t;
	}

	/**
	 * 管理员操作日志 价格变动或者 暂停才会触发此方法
	 * $data=[
	 *
	 * 'name'=>'报价手机全名',
	 * 'before'=>[
	 *                'inner_screen'=>'变动前内屏报价',
	 *                'outer_screen'=>'变动前外屏报价'
	 *            ],
	 *  'after'=>[
	 *                'inner_screen'=>'变动后内屏报价',
	 *                'outer_screen'=>'变动后外屏报价'
	 *            ]
	 * ]
	 */
	public static function addOfferLog($offer_id,$data,$handle_type=0)
	{
		$model = new BrandOfferUpdateLog();
		$model->offer_id = $offer_id;
		$model->handle_type = $handle_type;
		if($handle_type == self::__HD__UPDATE){
			$model->content = implode('<br>',self::tempFormat($data));
		}else{
			$model->content = $data['name'].'报价暂停使用';
		}

		$model->uid = Yii::$app->user->identity->id;
		$model->name = Yii::$app->user->identity->username;
		$model->update_time = date('Y-m-d H:i:s',time());

		if($model->insert(false)){
			//add change attention  把使用中的报价 改为暂停并且更新变动提醒
			$d = array();
			$d['offer_change_log_id'] = $model->id;
			$d['update_time'] = date('Y-m-d H:i:s',time());
			$d['status']= MaintenanceOffer::__STATUS_STOP;
			$w = array();
			$w['offer_id'] = $offer_id;
			$w['status'] = MaintenanceOffer::__STATUS_START;
			MaintenanceOffer::updateChangeLog($d,$w);

			return $model;
		}
		return false;
	}

	/**
	 * 日志内容数据格式化模板
	*/
	public static function tempFormat($data)
	{
		$head_temp = $data['name'].'报价变动：';
		list($inner_screen,$outer_screen) = array_values($data['before']);
		$before_temp = sprintf("前：内屏报价<span class='font-green-sharp'>%s</span>&nbsp;外屏报价<span class='font-green-sharp'>%s</span>",$inner_screen,$outer_screen);
		$inner_screen = $outer_screen = null;
		list($inner_screen,$outer_screen) = array_values($data['after']);
		$after_temp = sprintf("后：内屏报价<span class='font-red-thunderbird'>%s</span>&nbsp;外屏报价<span class='font-red-thunderbird'>%s</span>",$inner_screen,$outer_screen);
		return [$head_temp,$before_temp,$after_temp];
	}

}
