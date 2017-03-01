<!-- BEGIN FORM-->
<form action="" id="role_form" method="POST" class="form-horizontal">
	<div class="form-body">
		<div class="form-group">
			<label class="control-label col-md-3">配置项目
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<?= \yii\helpers\Html::radioList('group_id',$model->group_id,\common\models\Conf::$confType,['class'=>'btn gray'])?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">键名Key
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="name" value="<?= $model->name ?>" class="form-control" /> 不区分大小写 </div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">值Value
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="value" value="<?= $model->value ?>" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">中文描述
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="china_name" value="<?= $model->china_name ?>" class="form-control" />
			</div>
		</div>

	</div>
	<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
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

		var form1 = $('#role_form');
		form1.validate({
			errorElement: 'span', //default input error message container
			errorClass: 'help-block help-block-error', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			ignore: "",  // validate all fields including form hidden input
			messages: {
				group_id: {
					required: '必选项'
				},
				name: {
					required: '必填项'
					minlength: '至少2个字符'
				},
				value:{
					required: '必填项'
				},
				china_name:{
					required: '必填项',
					maxLength:'至多可填100个字符'
				}
			},
			rules: {
				group_id: {
					required: true
				},
				name: {
					required: true,
					minlength: 2
				},
				value:{
					required: true
				},
				china_name:{
					required: true,
					maxLength:100
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