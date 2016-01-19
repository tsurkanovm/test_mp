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

        $upload_form = $this->getReadUploadForm();

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
        $upload_form = $this->getReadUploadForm();
        $this->validateReadUploadForm( $upload_form );

        $file_path = Yii::$aliases['@file_path'] . '/' . $upload_form->file;
        $parser_config = $this->getScenarioParameter('parser_config');
        $basic_columns = $this->getScenarioParameter('basic_columns');
        $last_line = ($upload_form->read_line_end == 'все') ? 0 : $upload_form->read_line_end;
        $custom_settings = ['last_line' => $last_line, 'has_header_row' => false];

        $parser = Yii::$app->controller->module->multiparser;
        $parser->setConfiguration( $parser_config );
        $data = $parser->parse( $file_path, $custom_settings );

        $write_upload_form = $this->getReadUploadForm();
        return $this->renderAjax('index', [
            'options' => [
                'mode' => 'data',
                'data' => $data,
                'model' => $write_upload_form,
                'basic_columns' => $basic_columns,
            ]
        ]);

//        if (file_exists($file_path)) {
//            unlink($file_path);
//        }

    }

    public function actionWrite(){
       $model = $this->getWriteUploadForm();
        $this->validateWriteUploadForm($model);
        // валидация форм
        // сборка конфигурации парсера
        // парсинг
        // запись в БД
        // удаление файла, при завершении, при отказе от записи и новом чтении (кеш?)
        // показ результата (лог)


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

    protected function validateReadUploadForm(&$model)
    {
        if ($model->load(Yii::$app->request->post())) {
            $model->file = Yii::$app->session->getFlash('file_name');

            if (!$model->validate()) {
                // handle with error validation form
                $this->generateValidateErrorException($model);
            }
        } else {

            throw new HttpException(200, 'Ошибка загрузки данных в форму');
        }

    }

    protected function validateWriteUploadForm($model)
    {
        if ($model->load(Yii::$app->request->post())) {

            if (!$model->validate()) {
                // handle with error validation form
               $this->generateValidateErrorException($model);
            }
//            //получим колонки которые выбрал пользователь
//            $arr_attributes = Yii::$app->request->post()['DynamicModel'];
//            //соберем модель по полученным данным
//            $model = DynamicFormHelper::CreateDynamicModel($arr_attributes);
//            //добавим правила валидации (колонки должны быть те что указаны в конфиге)
//            foreach ($arr_attributes as $key => $value) {
//                $model->addRule($key, 'in', ['range' => array_keys(Yii::$app->multiparser->getConfiguration('csv', 'basic_column'))]);
//            }
        } else {

            throw new HttpException(200, 'Ошибка загрузки данных в форму');
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

    protected function getReadUploadForm(){

        $parser_config = $this->getScenarioParameter('parser_config');
        $upload_form = new UploadFileParsingForm(['parser_config' => $parser_config]);
        $upload_form->scenario = UploadFileParsingForm::SCENARIO_READ;

        return $upload_form;
    }

    protected function getWriteUploadForm(){

        $upload_form = new UploadFileParsingForm();
        $upload_form->scenario = UploadFileParsingForm::SCENARIO_WRITE;

        return $upload_form;
    }

    protected function generateValidateErrorException( $model ){

        $errors_str = 'Error upload form:';
        foreach ( $model->getErrors() as $error ) {
            $errors_str .= ' ' . implode( array_values( $error ) );
        }

        throw new HttpException( 200, $errors_str );

    }
}

