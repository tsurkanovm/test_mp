<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use common\modules\parser\widgets\parser_view\ParserViewAsset;

ParserViewAsset::register($this);
$title = '';
if ( !empty( $params['title'] ) ) {
    $title = $params['title'];
}
echo Html::tag( 'h3', $title );
?>

<div class="row">

        <?php
        $show_arr = [10, 100, 'все'];
        $model = false;
        if ( !empty( $params['model'] ) ) {
            $model = $params['model'];
        }

        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'read-form', 'data-save_action' => Url::to( ['parser/save'] )],'action'=>['parser/read']]);

        $this->beginBlock('model_data');
        if ( $model ) {

            echo $form->field($model, 'file',['options' =>['class' => 'col-md-4']])->fileInput()->label( false );
            //echo Html::label('Показать','uploadfileparsingform-show' ,['options' =>['class' => 'col-md-1','display' => 'block']]);
            echo $form->field($model, 'show',['options' =>['class' => 'col-md-1']])->dropDownList(array_combine( $show_arr, $show_arr ) )->label( false );

        }
        $this->endBlock();

        echo $this->blocks['model_data'];


        echo Html::tag('div', Html::submitButton('Прочитать', ['class' => 'btn btn-primary']),['class' => 'form-group col-md-2']);

        echo Html::tag('div','',['id' => 'data-container']);
        ActiveForm::end();
        ?>

</div>