<!-- BEGIN FORM-->
<form action="" id="admin_form" method="POST" class="form-horizontal">
	<div class="form-body">

		<div class="form-group">
			<label class="control-label col-md-3">手机号码
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="tel" name="phone" placeholder="输入11位有效手机号码" autocomplete="off"  value="<?= $model->phone ?>"  class="form-control" /> </div>
		</div>

		<div class="form-group">
			<label class="control-label col-md-3">短信内容
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<textarea name="content" value="<?= $model->content ?>"  class="form-control" placeholder="请输入短信内容控制在70个汉字以内" rows="3"></textarea>
			</div>
		</div>

	</div>
	<input type="hidden"  name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
	<div class="form-actions">
		<div class="row">
			<div class="col-md-offset-3 col-md-9">
				<button type="submit" class="btn green">确认发送</button>

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

		var form1 = $('#admin_form');
		form1.validate({
			errorElement: 'span', //default input error message container
			errorClass: 'help-block help-block-error', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			ignore: "",  // validate all fields including form hidden input

			rules: {

				phone:{
					required: true,
					digits: true,
					rangelength: [11, 11]
				},
				content: {
					required: true,
					rangelength: [10, 200]
				}
			},
			messages: {

				phone:{
					required: "非空字段",
					digits: "请输入有效手机号码",
					rangelength: '请输入11位有效手机号码'
				},
				content: {
					required: "非空字段",
					rangelength: '请输入短信内容控制在70个汉字以内'
				}
			},

			invalidHandler: function (event, validator) {
				showToastr('error','表单项验证有误');
			},

			highlight: function (element) {
				$(element)
					.closest('.form-group').addClass('has-error');
			},

			unhighlight: function (element) {
				$(element)
					.closest('.form-group').removeClass('has-error');
			},

			success: function (label) {
				label
					.closest('.form-group').removeClass('has-error');
			},

			submitHandler: function (form) {

				form.submit();
			}
		});

	})

</script>