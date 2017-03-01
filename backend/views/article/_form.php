<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="col-lg-2 col-sm-1"></div>
<div class="col-lg-5 col-sm-7 container">

    <?php $form = ActiveForm::begin(['options'=>['class'=>'bs-docs-example form-horizontal','id'=>'article-reate-from']]); ?>
        <?= $form->field($model, 'category_id')->dropDownList($ArticleCategoryList) ?>
        <?= $form->field($model, 'coverage_type_id')->dropDownList($coverage) ?>
        <?= $form->field($model, 'tag_id')->radioList([1=>'保险详情', 2=> '投保须知',3=>'理赔详情']);?>
        <?= $form->field($model, 'title') ?>
        <?= $form->field($model, 'sort')->textInput(['placeholder'=>'请输入（0-255）由小到大排序']);?>
    <?= $form->field($model, 'status')->radioList([1=>'启用', 0=> '禁用']);?>

    <?= $form->field($model, 'content')->widget(\yii\redactor\widgets\Redactor::className(),[
            'clientOptions' => [
                'lang' => 'zh_cn',
                'plugins' => ['clips', 'fontcolor','imagemanager']
            ]
        ]) ?>
        <div class="form-group">
            <?= Html::activeHiddenInput($model,'id') ?>
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $('#article-category_id').on('change',function(){
            var type  = $(this).val();
            if(type > 0){
                $('.field-article-coverage_type_id,.field-article-tag_id').hide();
            }else{
                $('.field-article-coverage_type_id,.field-article-tag_id').show();
            }
        });


    });
</script>
