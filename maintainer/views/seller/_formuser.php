<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Seller */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row ">
    <div class="col-sm-6 col-md-4 col-md-offset-2 col-sm-offset-1">
<?php $form = ActiveForm::begin(['id'=>'createSeller',
    'enableClientValidation'=>true,
    'enableAjaxValidation'=>false,
    'options'=>['class'=>'form-horizontal'],
    'validationUrl'=>['seller/check'],
    'encodeErrorSummary' => true,
    'validateOnSubmit' => true,
    'errorCssClass' => 'has-error',
    'successCssClass' => 'has-success',
    'validatingCssClass' => 'validating',
    'ajaxParam' => 'ajax',
    'ajaxDataType' => 'json',
    'scrollToError' => true,

]); ?>
    <?=Html::hiddenInput('_csrf-maintainer',Yii::$app->request->csrfToken)?>

<?= $form->field($model, 'name',['enableAjaxValidation'=>true,'enableClientValidation'=>true])->textInput(['maxlength' => true,'class'=>'form-control']) ?>

<?= $form->field($model, 'passwd')->passwordInput(['maxlength' => true,'class'=>'form-control']) ?>
<div class="form-group">
    <?= Html::submitButton('下一步', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
    </div>
</div>

