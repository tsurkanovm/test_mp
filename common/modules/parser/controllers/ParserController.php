<?php
namespace common\modules\parser\controllers;

use common\components\CustomVarDamp;
use common\modules\parser\models\UploadFileParsingForm;
use Yii;
use yii\base\UserException;
use yii\data\ArrayDataProvider;
use yii\multiparser\DynamicFormHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * Site controller
 */
class ParserController extends Controller
{

    public function actionIndex($scenario = '')
    {

        // CustomVarDamp::dumpAndDie(Yii::$app->controller->module);
        $model = new UploadFileParsingForm();
        return $this->render('index', [
            'options' => ['model' => $model,
                'title' => 'Вставьте свой заголовок сценария'
            ],
        ]);
    }

    public function actionSave()
    {
        $post = Yii::$app->request->post();

        if (empty($post) && !empty($_FILES)) {
            if (move_uploaded_file($_FILES[0]['tmp_name'], Yii::$aliases['@file_path'] . '/' . basename($_FILES[0]['name']))) {
                Yii::$app->session->setFlash( 'file_name', basename($_FILES[0]['name']) );
                return true;
            } else {
                return false;
            }
        }
    }

    public function actionRead()
    {

        $model = new UploadFileParsingForm();
        $this->validateUploadForm($model);
        $file_path = Yii::$aliases['@file_path'] . '/' . $model->file;



        $data1 = [0 => ['first','second','3'],1 => ['first1','second1','31']];
        //$data = Yii::$app->controller->module->multiparser->parse($model->file, ['mode' => 'template']);
        // parse -> data, read from $model->tempName
        // slice -> data

        //Yii::$app->getCache()->set('parser_data', json_encode($data), 300);
         //return json_encode( $data1 );
        //return 45;

        return $this->renderAjax('index', [
            'options' => ['title' => 'Прочитанные данные',
                'mode' => 'data',
                'data' => $data1,
            ]
        ]);


        if (file_exists($file_path)) {
            unlink($file_path);
        }

    }

    public function validateUploadForm(&$model)
    {

        if ($model->load(Yii::$app->request->post())) {
            $model->file = Yii::$app->session->getFlash('file_name');

            if (!$model->validate()) {
                // handle with error validation form
                $errors_str = 'Error upload form:';
                foreach ($model->getErrors() as $error) {
                    $errors_str .= ' ' . implode(array_values($error));
                }

                throw new HttpException(200, $errors_str);
            }
        } else {

            throw new HttpException(200, 'Ошибка загрузки данных в форму');
        }

    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            $msg = $exception->getMessage();

            if (Yii::$app->has('response')) {
                $response = Yii::$app->getResponse();
            } else {
                $response = new Response();
            }

            $response->data = $this->renderAjax('index', [
                'options' => ['title' => $msg,
                    'mode' => 'message']
            ]);

            return $response;
        }
    }


}
