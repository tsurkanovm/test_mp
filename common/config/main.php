<?php
$mp_configuration = require( __DIR__ . '/../components/parser/config.php');
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'multiparser'=>[
            'class' => 'yii\multiparser\YiiMultiparser',
            'configuration' => $mp_configuration,
        ],
    ],
];
// "minimum-stability": "stable",