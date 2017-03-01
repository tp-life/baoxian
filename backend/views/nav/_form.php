<!-- BEGIN FORM-->
<form action="" id="module_form" method="POST" class="form-horizontal">
	<div class="form-body">
		<div class="form-group">
			<label class="control-label col-md-3">子菜单名称
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="name"  value="<?= $model->name ?>"  class="form-control" /></div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">菜单Icon
				<span class="required"> </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="icon" value="<?= $model->icon  ?>"  class="form-control" />
			<a href="http://fortawesome.github.io/Font-Awesome/icons/"  class="form-control btn default" >参考地址：http://fortawesome.github.io/Font-Awesome/icons/</a>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">排序
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" name="sort" value="<?= $model->sort  ?>"  class="form-control" /> [0-99] 菜单显示按此升序排列</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">是否有效
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<select class="form-control" name="is_effect">
					<option  value="0">否</option>
					<option <?php if($model->is_effect): ?> selected <?php endif; ?> value="1">是</option>
				</select>
			</div>
		</div>


	</div>
	<input type="hidden" name="nav_id" value="<?= $model->nav_id ?>">
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

		var form1 = $('#module_form');
		form1.validate({
			errorElement: 'span', //default input error message container
			errorClass: 'help-block help-block-error', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			ignore: "",  // validate all fields including form hidden input
			messages: {
				name: {
					required: '必填项',
					rangelength:'菜单名字在2-30位字符'
				},
				sort: {
					required: '必填项',
					digits:'请输入数字',
					range:'请输入[0-99]数字'
				},
				is_effect: {
					required: '必填项'
				}
			},
			rules: {
				name: {
					required: true,
					rangelength:[2,30]
				},
				sort: {
					required: true,
					digits:true,
					range:[0,99]
				},
				is_effect: {
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