<!-- BEGIN FORM-->
<form action="" id="admin_form" method="POST" class="form-horizontal">
	<div class="form-body">
		<div class="form-group">
			<label class="control-label col-md-3">用户名
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="text" id="username" name="username" <?php if(!$model->isNewRecord): ?> readonly="" <?php endif; ?> value="<?= $model->username ?>" autocomplete="off"  placeholder="Username"  class="form-control" /> </div>
		</div>

		<div class="form-group">
			<label class="control-label col-md-3">密码
				<span class="required"> <?php if($model->isNewRecord): ?> * <?php endif; ?></span>
			</label>
			<div class="col-md-4">
				<input type="password" name="password" value="" placeholder="Password" autocomplete="off"  class="form-control" /><?php if(!$model->isNewRecord): ?> 为空表示不修改密码 <?php endif; ?> </div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">手机号码
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<input type="tel" name="phone" placeholder="Phone" autocomplete="off"  value="<?= $model->phone ?>"  class="form-control" /> </div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">系统管理
				<span class="required"> </span>
			</label>
			<div class="col-md-4">
				<select class="form-control" name="is_system">
					<option  value="0">否</option>
					<option <?php if($model->is_system): ?> selected <?php endif; ?> value="1">是</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">是否启用
				<span class="required">  </span>
			</label>
			<div class="col-md-4">
				<select class="form-control" name="status">
					<option  value="0">否</option>
					<option <?php if($model->status): ?> selected <?php endif; ?> value="1">是</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">角色分组
				<span class="required"> * </span>
			</label>
			<div class="col-md-4">
				<?= \yii\helpers\Html::dropDownList('role_id',$model->role_id,$roleArray,['prompt'=>'选择角色','class'=>'form-control form-filter']) ?>
			</div>
		</div>

	</div>
	<input type="hidden"  name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
	<div class="form-actions">
		<div class="row">
			<div class="col-md-offset-3 col-md-9">
				<button type="submit" class="btn green">确定</button>
				<button type="reset" class="btn grey-salsa btn-outline">取消</button>
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
				<?php if($model->isNewRecord): ?>
					username: {
					required: true,
					minlength: 4,
					remote: {
						url: '<?= \yii\helpers\Url::to(['admin/ckadmin']) ?>',
						type: 'GET',
						data: {
								username: function () {
									return $('#username').val();
								}
							}

					}
				},
				<?php endif; ?>
				password:{
					<?php if($model->isNewRecord): ?>
					required: true,
					<?php endif; ?>
					rangelength:[6,12]
				},
				phone:{
					required: true,
					digits: true,
					rangelength: [11, 11]
				},
				role_id: {
					required: true
				}
			},
			messages: {
				<?php if($model->isNewRecord): ?>
				username: {
					required: "非空字段",
					minlength: '请至少输入4位字符',
					remote: '用户已经存在'
				},
				<?php endif; ?>
				password:{
					<?php if($model->isNewRecord): ?>
					required: "非空字段",
					<?php endif; ?>
					rangelength:'密码在6-12位字符'
				},
				phone:{
					required: "非空字段",
					digits: "请输入有效手机号码",
					rangelength: '请输入11位有效手机号码'
				},
				role_id: {
					required: "非空字段"
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