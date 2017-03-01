<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 14:05
 */

namespace common\wxpay;
require "lib/WxPay.Data.php";
require_once "lib/WxPay.Config.php";

class WxHelp
{
    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        if(class_exists($name)){
            return new $name($arguments);
        }
        return new \stdClass();
    }

    public static function getWxConfig($name=''){
        if(!$name){
            return '';
        }
        $name ='get'.strtoupper($name);
        return \WxPayConfig::$name();
    }
}