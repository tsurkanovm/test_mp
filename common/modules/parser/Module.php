<?php
namespace common\modules\parser;

class Module extends \yii\base\Module
{
	public function init()
	{
		parent::init();
		
		\Yii::configure($this, require(__DIR__.'/config.php'));

        //register custom error handler for module
        $handler = new \yii\web\ErrorHandler;
        $handler->errorAction = 'parser/parser/error';
        \Yii::$app->set('errorHandler', $handler);
        $handler->register();
	}




}
