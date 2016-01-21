<?php
/**
 * Created by PhpStorm.
 * User: Tsurkanov
 * Date: 21.01.2016
 * Time: 17:19
 */

namespace common\modules\parser\components;

use yii\base\Model;
interface MassiveDataValidatorInterface {

    public function validate( $data );
    public function getMassage();
    public function hasError();
    public function setModel( Model $model );
    public function close();

}