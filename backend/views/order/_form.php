<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'member_id')->textInput() ?>

    <?= $form->field($model, 'member_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_time')->textInput() ?>

    <?= $form->field($model, 'order_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_state')->textInput() ?>

    <?= $form->field($model, 'coverage_id')->textInput() ?>

    <?= $form->field($model, 'coverage_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coverage_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coverage_type')->textInput() ?>

    <?= $form->field($model, 'coverage_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_delay')->textInput() ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'is_pre_vest_order')->textInput() ?>

    <?= $form->field($model, 'buyer_msg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_type')->textInput() ?>

    <?= $form->field($model, 'order_from')->textInput() ?>

    <?= $form->field($model, 'add_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
