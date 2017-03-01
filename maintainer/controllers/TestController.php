<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 11:07
 */

namespace maintainer\controllers;


use common\library\helper;
use common\models\Order;
use yii\data\DataProviderInterface;
use yii\web\Controller;

class TestController extends Controller
{
    public function actionTest(){
//        var_dump(\Yii::$app->params['concat']);
//        var_dump(helper::handleMsg('phoneErr'));
//        $data=['code'=>'666666','time'=>date('Y-m-d'),'tel'=>'123'];
//        echo   helper::handleMsg('security',['order_sn'=>'erwerwerwe','start'=>'2015-15-45','end'=>'2016-41-12']);
        $a=Order::findOne(['order_id'=>18963])->toArray();
        var_dump($a);
    }

}