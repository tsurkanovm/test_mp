<?php

return [
	'components' => [
        'multiparser'=>[
            'class' => 'yii\multiparser\YiiMultiparser',
            'configuration' => require(__DIR__.'/parser_config.php'),
        ],
	],
	'params' => [
	//	'file_path' => $file_path,
	],
];
