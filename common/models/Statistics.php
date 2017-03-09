<?php

namespace common\models;

use Yii;


class Statistics extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_seller_statistics}}';
    }

    //卡券不足提醒数量
    const DEFI_CARD_NUM = 20;

}
