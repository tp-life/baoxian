<?php
use yii\helpers\Html;
?>
<!-- BEGIN FORM-->
<form action="" id="module_fun_form" method="POST" class="form-horizontal">
	<div class="form-body">
		<div class="form-group">
			<label class="control-label col-md-3">方法名称
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="action"  value="<?= $model->action ?>"  class="form-control" /> 不区分大小写</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">中文名称
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="name" value="<?= $model->name ?>"  class="form-control" /> </div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">菜单类别
				<span class="required">  </span>
			</label>
			<div class="col-md-4">
				<?= Html::dropDownList('group_id',$model->group_id,$navArray,['prompt'=>'选择菜单组','class'=>'form-control form-filter']) ?>
				如果是栏目操作内方法选项，不用选择 Eg:create delete 等
			</div>
		</div>

	</div>
	<input type="hidden" name="module_id" value="<?= $model->module_id ?>">
	<input type="hidden" id="_csrf-backend" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
	<div class="form-actions">
		<div class="row">
			<div class="col-md-offset-3 col-md-9">
				<button type="submit" class="btn green">Submit</button>
				<button type="reset" class="btn grey-salsa btn-outline">Cancel</button>
			</div>
		</div>
	</div>
</form>
<!-- END FORM-->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<script type="text/javascript">

	$(function(){
		var form1 = $('#module_fun_form');
		form1.validate({
			errorElement: 'span', //default input error message container
			errorClass: 'help-block help-block-error', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			ignore: "",  // validate all fields including form hidden input
			messages: {
				select_multi: {
					maxlength: jQuery.validator.format("Max {0} items allowed for selection"),
					minlength: jQuery.validator.format("At least {0} items must be selected")
				}
			},
			rules: {
				action: {
					minlength: 2,
					required: true
				},
				name: {
					minlength: 2,
					required: true
				}

			},

			invalidHandler: function (event, validator) { //display error alert on form submit
				//success1.hide();
				//error1.show();
				//App.scrollTo(error1, -200);
				showToastr('error','表单项验证有误');
			},

			highlight: function (element) { // hightlight error inputs
				$(element)
					.closest('.form-group').addClass('has-error'); // set error class to the control group
			},

			unhighlight: function (element) { // revert the change done by hightlight
				$(element)
					.closest('.form-group').removeClass('has-error'); // set error class to the control group
			},

			success: function (label) {
				label
					.closest('.form-group').removeClass('has-error'); // set success class to the control group
			},

			submitHandler: function (form) {
				//success1.show();
				//error1.hide();
				//showToastr('info','验证成功');
				form.submit();
			}
		});

	})

</script>