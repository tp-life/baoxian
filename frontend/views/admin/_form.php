<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(['id'=>'admin_form',
		'enableClientValidation'=>true,
		'enableAjaxValidation'=>false,
		'options'=>['class'=>'form-horizontal'],
		'validationUrl'=>['admin/ckform'],
		'encodeErrorSummary' => true,
		'validateOnSubmit' => true,
		'errorCssClass' => 'has-error',
		'successCssClass' => 'has-success',
		'validatingCssClass' => 'validating',
		'ajaxParam' => 'ajax',
		'ajaxDataType' => 'json',
		'scrollToError' => true,

	]); ?>
    <?= $form->field($model, 'username',['enableAjaxValidation'=>true,'enableClientValidation'=>true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone',['enableAjaxValidation'=>true,'enableClientValidation'=>true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_system')->textInput() ?>

    <?= $form->field($model, 'role_id')->textInput() ?>

    <?= $form->field($model, 'login_at')->textInput() ?>

    <?= $form->field($model, 'login_ip')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>