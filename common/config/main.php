<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'=>'zh-cn',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		//终端设备探测器
		'mobileDetect' =>[
			'class'=>'common\tool\MobileDetect'
		],

		//只针对数字加密解密工具
		'numberCode' =>[
			'class'=>'common\tool\NumberCode'
		],
    ],
    'modules' => [
        'redactor' => [
            'class'=>'yii\redactor\RedactorModule',
            'fileAllowExtensions'=>['zip','rar'],
            'imageAllowExtensions' => ['jpg', 'png', 'gif', 'bmp','jpeg'],
            'uploadDir' => '@webroot/uploads',
            'uploadUrl' => '@web/uploads'
        ],
		//文章模块  公共处理文章显示相关
		'marticle' => [
			'class' => 'common\modules\marticle\marticle',
		],
		//卡券统计 异步相关
		'mcoupon' => [
			'class' => 'common\modules\mcoupon\mcoupon',
		],
		//卡券导入 转化 快捷展示
		'mimport' => [
			'class' => 'common\modules\mimport\mimport',
		],
		//微信应用架构模块 API
		'mweixin' => [
			'class' => 'common\modules\mweixin\mweixin',
		],

    ],
];
