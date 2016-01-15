<?php
/**
 * Created by PhpStorm.
 * User: Tsurkanov
 * Date: 13.01.2016
 * Time: 13:51
 */

namespace common\modules\parser\widgets\parser;


use yii\base\Widget;

class ParserView extends Widget{
    public $options;
    public $mode = '';
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $view = $this->mode;
        if ( !$view ) {
            $view = 'frame';
        }
       return $this->render($view,
            [
                'params' => $this->options,

            ]);
    }


}