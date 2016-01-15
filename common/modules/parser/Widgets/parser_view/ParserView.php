<?php
/**
 * Created by PhpStorm.
 * User: Tsurkanov
 * Date: 13.01.2016
 * Time: 13:51
 */

namespace common\modules\parser\widgets\parser_view;


use yii\base\Widget;

class ParserView extends Widget{
    public $options;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if ( empty( $this->options['mode'] ) ) {
            $view = 'frame';
        } else{
            $view = $this->options['mode'];
        }
       return $this->render($view,
            [
                'params' => $this->options,

            ]);
    }


}