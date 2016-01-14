<?php
/**
 * Created by PhpStorm.
 * User: Cibermag
 * Date: 27.08.2015
 * Time: 16:47
 */
namespace common\components;
use yii\helpers\BaseVarDumper;

class CustomVarDamp extends BaseVarDumper  {

    public static function dumpAndDie($var, $depth = 10, $highlight = false)
    {
        echo "<pre>";
        echo static::dumpAsString($var, $depth, $highlight);
        echo "</pre>";
        die;
    }
    public static function dump($var, $step = '', $depth = 10, $highlight = false)
    {
        echo "<pre>";
        if ($step) {
            echo "-------------- {$step} -------------";
        }
        echo static::dumpAsString($var, $depth, $highlight);
        if ($step) {
            echo "-------------- {$step} -------------";
        }
        echo "</pre>";

    }
}