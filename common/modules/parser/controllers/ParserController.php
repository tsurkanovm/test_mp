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

       // CustomVarDamp::dumpAndDie(Yii::$app->controller->module->errorHandler);
        //CustomVarDamp::dumpAndDie($module = Yii::$app->controller->module->errorHandler);
        $model = new UploadFileParsingForm();
        return $this->render('index', [
            'options' => ['model' => $model,
            'title' => 'Вставьте свой заголовок сценария'
                ],
        ]);
    }

    public function actionRead()
    {

        $post = Yii::$app->request->post();

        $model = new UploadFileParsingForm();
        if( empty( $post ) &&  !empty( $_FILES ) ){
            if( move_uploaded_file( $_FILES[0]['tmp_name'], Yii::$aliases['@file_path'] . '/'.basename($_FILES[0]['name'])))
            {
                Yii::$app->session->setFlash('file_name', basename($_FILES[0]['name']));
                return true;
            }
            else
            {
                return false;
            }
        }

        $this->validateUploadForm( $model );

        return $this->renderAjax('index', [
            'options' => ['title' => 'Успешно',
                'mode' => 'message']
        ]);
            //$data = [];
            // parse -> data, read from $model->tempName
            // slice -> data

            //Yii::$app->getCache()->set('parser_data', json_encode($data), 300);
            // return json_encode( Yii::$app->request->post() );
            //return 45;


    }

    public function validateUploadForm( &$model )
    {

        if ( $model->load(Yii::$app->request->post()) ) {
            $model->file = Yii::$app->session->getFlash('file_name');

            if ( !$model->validate() ) {
                // handle with error validation form
                $errors_str = 'Error upload form:';
                foreach ( $model->getErrors() as $error ) {
                    $errors_str .= ' ' . implode(array_values($error));
                }

                throw new HttpException( 200, $errors_str );
            }
        } else{

            throw new HttpException( 200, 'Ошибка загрузки данных в форму' );
        }

    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;

        if ( $exception !== null ) {
            $msg =  $exception->getMessage();

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

    public function renderResultView($data )
    {
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        if ( empty( $data[0] ) ) {
            // если нет первого ряла - это xml custom-файл с вложенными узлами, массив ассоциативный (дерево),
            // такой массив нет возможности вывести с помощью GridView
            // просто выведем его как есть
            echo "<pre>";
            return print_r($data);
        }
        // если отпарсенные данные - ассоциативный массив, то пользователю нечего выбирать
        // но выведем его в GridView
        $assoc_data_arr = $this->is_assoc($data[0]);

        if ( $assoc_data_arr ) {

            // $mode == 'template' or xml file
            // парсинг с файла по шаблону
            // согласно конфигурационного файла у нас колонкам назначены ключи
            // то есть результат - ассоциативный массив, у пользователя нечего спрашивать
            // данные отконвертированы согласно настройкам и готовы к записи в БД (или к дальнейшей обработке)

            return $this->render('results',
                ['model' => $data,
                    // список колонок для выбора
                    'dataProvider' => $provider]);

        } else {
            // $mode == 'custom' and not xml
            // для произвольного файла создадим страницу предпросмотра
            // с возможностью выбора соответсвий колонок с отпарсенными данными
            //колонки для выбора возьмем из конфигурационного файла - опция - 'basic_column'

            // создадим динамическую модель на столько реквизитов сколько колонок в отпарсенном файле
            // в ней пользователь произведет свой выбор
            $last_index = end(array_flip($data[0]));
            $header_counts = $last_index + 1; // - количество колонок выбора формы предпросмотра
            $header_model = DynamicFormHelper::CreateDynamicModel($header_counts);

            // колонки для выбора возьмем из конфигурационного файла
            $basicColumns = Yii::$app->multiparser->getConfiguration($this->file_extension, 'basic_column');;

            return $this->render('results',
                ['model' => $data,
                    'header_model' => $header_model,
                    // список колонок для выбора
                    'basic_column' => $basicColumns,
                    'dataProvider' => $provider]);
        }

    }

    private function is_assoc(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

}
