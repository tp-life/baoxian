<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="col-lg-2 col-sm-1"></div>
<div class="col-lg-5 col-sm-7 container">

    <?php $form = ActiveForm::begin(['options'=>['class'=>'bs-docs-example form-horizontal']]); ?>
    <?= $form->field($model, 'pid')->dropDownList($ArticleCategoryList) ?>
    <?= $form->field($model, 'title') ?>
    <?= $form->field($model, 'sort')->textInput(['placeholder'=>'请输入（0-255）由小到大排序','class'=>'form-control']);?>
    <?= $form->field($model, 'brief')->textarea(['rows'=>5]);?>

    <div class="form-group">
        <?= Html::activeHiddenInput($model,'id') ?>
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
