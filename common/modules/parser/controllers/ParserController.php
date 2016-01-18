<?php
namespace common\modules\parser\controllers;

use common\components\CustomVarDamp;
use common\modules\parser\models\UploadFileParsingForm;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;



/**
 * Site controller
 */
class ParserController extends Controller
{

    public function actionIndex()
    {
        $title = $this->getScenarioParameter('title');
        // $require_columns = $this->getScenarioParameter('require_columns');

        $upload_form = $this->getUploadForm();

        return $this->render('index', [
            'options' => ['model' => $upload_form,
                'title' => $title
            ],
        ]);
    }

    public function actionSave()
    {
        $post = Yii::$app->request->post();

        if (empty($post) && !empty($_FILES)) {
            if (move_uploaded_file($_FILES[0]['tmp_name'], Yii::$aliases['@file_path'] . '/' . basename($_FILES[0]['name']))) {
                Yii::$app->session->setFlash('file_name', basename($_FILES[0]['name']));
                return true;
            } else {
                return false;
            }
        }
    }

    public function actionRead()
    {
        $upload_form = $this->getUploadForm();
        $this->validateUploadForm( $upload_form );

        $file_path = Yii::$aliases['@file_path'] . '/' . $upload_form->file;
        $parser_config = $this->getScenarioParameter('parser_config');
        $basic_columns = $this->getScenarioParameter('basic_columns');
        $last_line = ($upload_form->read_line_end == 'все') ? 0 : $upload_form->read_line_end;
        $custom_settings = ['last_line' => $last_line, 'has_header_row' => false];

        $parser = Yii::$app->controller->module->multiparser;
        $parser->setConfiguration( $parser_config );
        $data = $parser->parse( $file_path, $custom_settings );

        return $this->renderAjax('index', [
            'options' => [
                'mode' => 'data',
                'data' => $data,
                'basic_columns' => $basic_columns,
            ]
        ]);

//        if (file_exists($file_path)) {
//            unlink($file_path);
//        }

    }

    protected function getUploadForm(){

        $parser_config = $this->getScenarioParameter('parser_config');
        $upload_form = new UploadFileParsingForm(['parser_config' => $parser_config]);

        return $upload_form;
    }

    protected function validateUploadForm(&$model)
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

    protected function getScenarioParameter($parameter = '')
    {
        //$scenario = Yii::$app->request->get('scenario');
        $scenario = 'details';
        $configuration = Yii::$app->controller->module->params;
        if (empty($configuration['scenarios_config'])) {
            throw new HttpException(200, 'В модуле не определены настройки сценариев - module->params[\'scenarios_config\']');
        }
        $configuration = $configuration['scenarios_config'];
        if (empty($configuration[$scenario])) {
            throw new HttpException(200, "Модуль не поддерживает указанный сценарий  {$scenario}");
        }
        if ($parameter && empty($configuration[$scenario][$parameter])) {
            throw new HttpException(200, "Сценарий {$scenario} не содержит настройку {$parameter}");
        }

        if ($parameter) {
            return $configuration[$scenario][$parameter];
        } else {
            return $configuration[$scenario];
        }

    }

}
