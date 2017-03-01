<?php
#error_reporting(E_ALL ^ E_STRICT);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(E_ERROR);
$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php')

);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'weixin\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'enableCsrfValidation'=>false,
            'csrfParam' => '_csrf-app',
			'enableCookieValidation' =>false
        ],
        'user' => [
            'identityClass' => 'common\models\Member',
			'enableAutoLogin' => true,
			'enableSession'=>true,
			//'identityCookie' => ['name' => '__lehuanxin', 'httpOnly' => true,'secure'=>false,'domain'=>'.lehuanxin.com','expire'=>0,'path'=>'/'],
			'identityCookie' => ['name' => '', 'httpOnly' => true,'secure'=>false,'expire'=>0,'path'=>'/'],
			'loginUrl'=>['site/index']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-maintainer',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
			'enableStrictParsing' => false,  //不启用严格解析
//            'rules' => [
//				'<controller:(site)>/<action:\w+>' => 'site/<action>',
//				'<controller:\w+>/<action:\w+>' => 'mweixin/<controller>/<action>',
//				'<controller:\w+>/<action:\w*>' => 'mweixin/<controller>/index',
//            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'keyPrefix' => 'myapp',       // 唯一键前缀
        ],

    ],
    'params' => $params,
];
