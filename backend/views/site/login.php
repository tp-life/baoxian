<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\bootstrap\ActiveForm;
$this->title = 'Login';
?>

<!-- BEGIN LOGIN FORM -->
	<?php $form = ActiveForm::begin(['id'=>'login-form','method'=>'post','options'=>['class'=>'login-form']]) ?>
	<h3 class="form-title font-green">保险管理中心</h3>
	<div class="alert alert-danger display-hide">
		<button class="close" data-close="alert"></button>
		<span> Enter any username and password. </span>
	</div>
	<div class="form-group">
		<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
		<label class="control-label visible-ie8 visible-ie9">账号</label>
		<input class="form-control form-control-solid placeholder-no-fix" type="text" value="" autocomplete="off" placeholder="账号" name="LoginForm[username]" /> </div>
	<div class="form-group">
		<label class="control-label visible-ie8 visible-ie9">密码</label>
		<input class="form-control form-control-solid placeholder-no-fix" type="password" value="" autocomplete="off" placeholder="密码" name="LoginForm[password]" /> </div>
	<div class="form-actions">
		<button type="submit" class="btn green uppercase">登录</button>
		<label class="rememberme check">
			<input type="checkbox" name="LoginForm[remember]" value="1" checked />记住密码</label>
		<!--<a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>-->
	</div>
<?= $form->errorSummary($model,['class'=>'color-view bg-red-intense bg-font-red-intense bold uppercase']); ?>
<?php ActiveForm::end(); ?>
<!-- END LOGIN FORM -->
