<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\modules\parser\widgets\parser_view\ParserViewAsset;

ParserViewAsset::register($this);
$title = '';
if ( !empty( $params['title'] ) ) {
    $title = $params['title'];
}
echo Html::tag( 'h3', $title );
?>

<div class="row">
    <div class="col-lg-5">
        <?php
        $show_arr = [10, 100, 'все'];
        $model = false;
        if ( !empty( $params['model'] ) ) {
            $model = $params['model'];
        }

        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'read-form'],'action'=>['parser/read']]);

        $this->beginBlock('model_data');
        if ( $model ) {
            echo $form->field($model, 'file')->fileInput();
            echo $form->field($model, 'show')->dropDownList(array_combine( $show_arr, $show_arr ) );
        }
        $this->endBlock();

        echo $this->blocks['model_data'];
        echo Html::tag('div', Html::submitButton('Прочитать', ['class' => 'btn btn-primary']),['class' => 'form-group']);
        echo Html::tag('div','',['id' => 'data-container']);
        ActiveForm::end();
        ?>
    </div>
</div>