<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\InsuranceCoverage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="insurance-coverage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->textInput() ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_id')->textInput() ?>

    <?= $form->field($model, 'type_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coverage_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'period')->textInput() ?>

    <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'official_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wholesale_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_payment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coverage_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_more')->textInput() ?>

    <?= $form->field($model, 'is_delay')->textInput() ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'add_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
