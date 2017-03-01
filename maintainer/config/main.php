<?php
#error_reporting(E_ALL ^ E_STRICT);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(E_ERROR);
$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/menu.php')

);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'maintainer\controllers',
    'bootstrap' => ['log'],
    'modules' => [
		//文章模块  公共处理文章显示相关
		'marticle' => [
			'class' => 'common\modules\marticle\marticle',
		],
	],
    'components' => [
        'request' => [
            'enableCsrfValidation'=>true,
            'csrfParam' => '_csrf-maintainer',
        ],
        'user' => [
            'identityClass' => 'common\models\Member',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-maintainer', 'httpOnly' => true],
			'loginUrl'=>['site/login']
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
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
