<?php
namespace common\modules\parser;

class Module extends \yii\base\Module
{
	public function init()
	{
		parent::init();

        $this->setAliases([
            '@file_path' => dirname(__DIR__). '/files/',
        ]);

		\Yii::configure($this, require(__DIR__.'/config.php'));

        //register custom error handler for module
        $handler = new \yii\web\ErrorHandler;
        $handler->errorAction = 'parser/parser/error';
      //  $handler->discardExistingOutput = false;
        \Yii::$app->set('errorHandler', $handler);
        $handler->register();
	}




}
