<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Seller */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="seller-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'seller_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'member_id')->textInput() ?>

    <?= $form->field($model, 'is_insurance')->textInput() ?>

    <?= $form->field($model, 'is_repair')->textInput() ?>

    <?= $form->field($model, 'province_id')->textInput() ?>

    <?= $form->field($model, 'city_id')->textInput() ?>

    <?= $form->field($model, 'area_id')->textInput() ?>

    <?= $form->field($model, 'area_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'detail_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'concat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'concat_tel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'add_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
