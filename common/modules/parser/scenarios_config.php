<?php
return [
    'details' =>
        ['parser_config' =>
            ['csv' =>
                ['class' => 'yii\multiparser\CsvParser',
                    'converter_conf' => [
                        'class' => 'yii\multiparser\Converter',
                        'configuration' => ['encode' => []],
                    ],
                ],
                'xls' =>
                    ['class' => 'yii\multiparser\XlsParser',
                        'converter_conf' => [
                            'class' => 'yii\multiparser\Converter',
                            'configuration' => ['encode' => []],
                        ],
                    ],
            ],

            'basic_columns' => [
                Null => 'null',
                'Description' => 'Название',
                'Article' => 'Артикул',
                'Price' => 'Цена',
                'Brand' => 'Производитель',
                'Count' => 'Количество',
                'discount' => 'Скидка',
            ],

            'require_columns' => [
                'Article',
                'Brand',
            ],

            'writer' => 'common\modules\parser\components\DetailsWriter',
            'title' => 'Загрузка товаров',
        ],


];

