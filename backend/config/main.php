<?php
$params = array_merge(
    require(__DIR__ . '/params.php')
);

$config = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => '4JBP_6p9XvUCdyro4Mq1eDxUCOLkePS8',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'db' => require(dirname(__DIR__)."/config/db.php"),
        'user' => [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
            // 'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
            'enableStrictParsing' => true,
            'rules' => [
                'ping'  =>  'site/ping',
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/user',
                    'pluralize'     => false,
                    'tokens' => [
                        '{id}'             => '<id:\d+>',
                        '{email}'          => '<email>'
                    ],
                    'extraPatterns' => [
                        'POST login'        =>  'login',
                        'OPTIONS login'     =>  'options',
                        'POST signup'       =>  'signup',
                        'OPTIONS signup'     =>  'options',
                        'POST confirm'      =>  'confirm',
                        'OPTIONS confirm'     =>  'options',
                        'POST password-reset-request'       =>  'password-reset-request',
                        'OPTIONS password-reset-request'     =>  'options',
                        'POST password-reset-token-verification'       =>  'password-reset-token-verification',
                        'OPTIONS password-reset-token-verification'     =>  'options',
                        'POST password-reset'       =>  'password-reset',
                        'OPTIONS password-reset'     =>  'options',
                    ]
                ],
            ]
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if($response->format == 'html') {
                    return $response;
                }
                $responseData = $response->data;
                if(is_string($responseData) && json_decode($responseData)) {
                    $responseData = json_decode($responseData, true);
                }
                if($response->statusCode >= 200 && $response->statusCode <= 299) {
                    $response->data = [
                        'success'   => true,
                        'status'    => $response->statusCode,
                        'data'      => $responseData,
                    ];
                } else {
                    $response->data = [
                        'success'   => false,
                        'status'    => $response->statusCode,
                        'data'      => $responseData,
                    ];
                }
                return $response;
            },
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
