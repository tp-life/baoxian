<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');//后台管理
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@maintainer', dirname(dirname(__DIR__)) . '/maintainer');//维修商家
Yii::setAlias('@weixin', dirname(dirname(__DIR__)) . '/weixin');//微信轻应用
//微信授权配置
Yii::setAlias('@weixinSiteAuth','http:://api-baoxian.lehuanxin.com/site/auth');

