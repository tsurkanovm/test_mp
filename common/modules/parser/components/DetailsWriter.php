<?php
/**
 * Created by PhpStorm.
 * User: Cibermag
 * Date: 30.09.2015
 * Time: 9:34
 */

namespace common\modules\parser\components;


use app\models\DetailsTest;
use yii\base\ErrorException;
use backend\models\ImportersFiles;
use backend\models\Importers;
use common\models\Details;
use common\components\ModelArrayValidator;

/**
 * Class PriceWriter
 * @package common\components
 * записывает в БД отпарсенные данные
 * запись происходит в несколько таблиц
 */
class DetailsWriter extends  Writer
{

    public function writeToDB()
    {
        $model = $this->models[0];
        foreach($this->data as $row) {
            $validate_row[$model->formName()] = $row;
            // clear previous loading
            $attributes = $model->safeAttributes();
            foreach ( $attributes as $key => $value ) {
                $model->$value = '';
            }
            if ( $model->load( $validate_row ) && $model->save(false) ) {

//                return true;
//            } else{
//
//                return false;
            }
        }


    }


    protected function  writePriceInTransaction($details_model, $files_model, $update_date){


            //2. попытаемся вставить данные в БД с апдейтом по ключам
           // $details_model->manualInsert($this->data, $this->configuration['importer_id']);



    }


    protected function setModels(){

        array_unshift( $this->models, new DetailsTest() );

    }
}