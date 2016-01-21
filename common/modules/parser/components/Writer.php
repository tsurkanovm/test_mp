<?php
/**
 * Created by PhpStorm.
 * User: Cibermag
 * Date: 30.09.2015
 * Time: 9:34
 */

namespace common\modules\parser\components;


use yii\base\ErrorException;
use backend\models\ImportersFiles;
use backend\models\Importers;
use common\models\Details;
use common\components\ModelArrayValidator;


abstract class Writer
{
    /**
     * @var - массив с данными которые нужно записать
     */
    protected $data;

    /**
     * @var - сообщение валидатора об ошибках
     */
    protected $validated_msg;
    /**
     * @var - bool - есть ли ошибки валидации
     */
    protected $hasValidationError;
    /**
     * @var - array - list of models (active records) - when we write to
     */
    protected $models;
    /**
     * @var - object that implements MassiveDataValidatorInterface for validation data
     */
    protected $validator;


    public function __construct( $data )
    {
        set_time_limit(600);
        $this->data = $data;
        $this->setModels();

    }

   abstract protected function setModels();

   abstract protected function writeToDB();


    /**
     * @return mixed
     */
    public function getValidatedMsg()
    {
        return $this->validated_msg;
    }

    public function setValidator( MassiveDataValidatorInterface $validator ){

        $this->validator = $validator;
    }

    /**
     * @return mixed
     */
    public function hasValidationError()
    {
        return $this->hasValidationError;
    }


    public function write()
    {
        //3. провалидируем полученные данные моделью - Details
        $this->validateByModels();
        if ( empty($this->data) ) {
            // после валидации не осталось валидных данных для записи
            return false;
        }
            //5. запишем данные в связанные таблицы
       $this->writeToDB();

        return true;
    }

    protected function validateByModels(){

        foreach ( $this->models as $model ) {

            $this->validator->setModel( $model );
            $this->data = $this->validator->validate( $this->data );
            $this->validated_msg = $this->validated_msg . $this->validator->getMassage();
            $this->hasValidationError =  $this->validator->hasError();

        }

        $this->validator->close();

    }
}