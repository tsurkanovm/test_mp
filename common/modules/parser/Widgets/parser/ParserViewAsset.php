<?php
/**
 * Created by PhpStorm.
 * User: Tsurkanov
 * Date: 14.01.2016
 * Time: 12:03
 */

namespace common\modules\parser\widgets\parser;
use yii\web\AssetBundle;


class ParserViewAsset extends AssetBundle {

    public $sourcePath = '@common/modules/parser/widgets/parser/js';
    public $js = [
        'parser-view.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
