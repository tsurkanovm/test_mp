<?php
namespace common\modules\parser\controllers;

use common\components\CustomVarDamp;
use common\modules\parser\models\UploadFileParsingForm;
use Yii;
use yii\multiparser\DynamicFormHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\Session;


/**
 * Site controller
 */
class ParserController extends Controller
{

    public function actionIndex()
    {

        $title = $this->getScenarioParameter('title');

        $upload_form = $this->getReadUploadForm();

        return $this->render('index', [
            'options' => ['model' => $upload_form,
                'title' => $title
            ],
        ]);
    }

    public function actionSave()
    {

        if (empty($post) && !empty($_FILES)) {
            $file_name = Yii::$aliases['@file_path'] . '/' . basename($_FILES[0]['name']);
            if (move_uploaded_file($_FILES[0]['tmp_name'], $file_name )) {

                if ($file_path = Yii::$app->session->hasFlash('file_name')) {
                    if(file_exists($file_path))
                        unlink($file_path);
                }

                Yii::$app->session->setFlash('file_name', $file_name, false);
                return true;
            } else {
                return false;
            }
        }
    }

    public function actionRead()
    {
        $upload_form = $this->getReadUploadForm();
        $this->validateUploadForm( $upload_form );

        $file_path = Yii::$aliases['@file_path'] . '/' . $upload_form->file;
        $basic_columns = $this->getScenarioParameter('basic_columns');
        $last_line = ($upload_form->read_line_end == 'все') ? 0 : $upload_form->read_line_end;
        $custom_settings = ['last_line' => $last_line, 'file_path' => $file_path];

        $data = $this->parseDataBySettings($custom_settings);

        $write_upload_form = $this->getReadUploadForm();
        return $this->renderAjax('index', [
            'options' => [
                'mode' => 'data',
                'data' => $data,
                'model' => $write_upload_form,
                'basic_columns' => $basic_columns,
            ]
        ]);


    }

    public function actionWrite(){
        // static fields
       $model = $this->getWriteUploadForm();
        $this->validateUploadForm($model);
        // dynamic fields
        $this->validateDynamicUploadForm();
       // prasing settings
        $first_line = (!$model->write_line_begin) ? 0 : $model->write_line_begin;
        $last_line = (!$model->write_line_end) ? 0 : $model->write_line_end;
        $custom_settings = ['last_line' => $last_line,
                            'first_line' => $first_line,
        ];


        $data = $this->parseDataBySettings($custom_settings);

        $file_path = Yii::$app->session->getFlash( 'file_name', null, true );
        if(file_exists($file_path))
            unlink($file_path);

        CustomVarDamp::dumpAndDie($data);

        // установка ключей
        // запись в БД
        // удаление файла, при завершении, при отказе от записи и новом чтении (кеш?)
        // показ результата (лог)

        $log = 'Successful';
        return $this->renderAjax('index', [
            'options' => [
                'mode' => 'message',
                'title' => $log,
            ]
        ]);






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

    protected function parseDataBySettings($custom_settings){
        if ( empty( $custom_settings['file_path'] ) ) {
            $file_path = Yii::$app->session->getFlash('file_name');
        } else {
            $file_path = $custom_settings['file_path'];
            unset( $custom_settings['file_path'] );
        }
        $parser_config = $this->getScenarioParameter('parser_config');
        array_merge( $custom_settings, ['has_header_row' => false] );

        $parser = Yii::$app->controller->module->multiparser;
        $parser->setConfiguration( $parser_config );
        $data = $parser->parse( $file_path, $custom_settings );

        return $data;
}

    protected function validateUploadForm(&$model)
    {
        if ($model->load(Yii::$app->request->post())) {
            if( isset( $model->file ) ){
                $model->file = Yii::$app->session->getFlash('file_name');
            }

            if (!$model->validate()) {
                // handle with error validation form
                $this->generateValidateErrorException($model);
            }
        } else {

            throw new HttpException(200, 'Ошибка загрузки данных в форму');
        }

    }



    protected function validateDynamicUploadForm(){
        //получим колонки которые выбрал пользователь
        $arr_attributes = Yii::$app->request->post()['DynamicModel'];
        //соберем модель по полученным данным
        $dynamic_model = DynamicFormHelper::CreateDynamicModel($arr_attributes);

        $require_columns = $this->getScenarioParameter('require_columns');
        $basic_columns = $this->getScenarioParameter('basic_columns');

        //добавим правила валидации (колонки должны быть те что указаны в конфиге)
        foreach ($arr_attributes as $key => $value) {
            $dynamic_model->addRule($key, 'in', ['range' => array_keys($basic_columns)]);
            // ищем наличие обязательных колонок
            $find_key = array_search( $value, $require_columns );
            if( $find_key !== false){
                unset( $require_columns[$find_key] );
            }
        }
        if( $require_columns ) {
            throw new HttpException( 200, implode(' - обязательное поле, укажите соответствие, ', $require_columns) . ' - обязательное поле, укажите соответствие' );
        }
        if (!$dynamic_model->validate()) {
            // handle with error validation form
            $this->generateValidateErrorException($dynamic_model);
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

        $errors_str = 'Ошибка формы загрузки данных:';
        foreach ( $model->getErrors() as $error ) {
            $errors_str .= ' ' . implode( array_values( $error ) );
        }

        throw new HttpException( 200, $errors_str );

    }
}

