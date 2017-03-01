<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/28
 * Time: 14:18
 */

namespace weixin\controllers;


use common\library\helper;
use common\wxpay\Wpay;
use common\wxpay\WxHelp;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\Cookie;

class TestController extends Controller
{
    public function actionIndex(){
//        \Yii::$app->response->cookies->add(new Cookie(['name'=>'test','value'=>'sdf']));
    setcookie('test123','shenm');
    }

}