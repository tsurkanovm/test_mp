<?php
use yii\widgets\ActiveForm;
use \yii\multiparser\DynamicFormHelper;
use yii\helpers\ArrayHelper;
use common\modules\parser\widgets\parser_view\ParserViewAsset;

    $form = ActiveForm::begin(['action' => 'write']);

echo DynamicFormHelper::CreateGridWithDropDownListHeader($dataProvider, $form, $header_model, $basic_column);

    ActiveForm::end();
