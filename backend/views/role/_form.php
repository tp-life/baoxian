<!-- BEGIN FORM-->
<link href="<?= Yii::getAlias('@css'); ?>/skins/line/green.css" rel="stylesheet">
<style>
	td{ padding:5px; line-height:21px; }
	td{ border:1px solid #D2D2D2; padding:5px;}
	table{border-collapse:collapse;width:100%;}
	div.checker input{
		opacity: 0.5;
	}
	.icheckbox{ display: inline}
</style>

<script src="<?= Yii::getAlias('@js'); ?>/icheck.min.js" type="text/javascript"></script>
<form action="" id="role_form" method="POST" class="form-horizontal">
	<div class="form-body">
		<div class="form-group">
			<label class="control-label col-md-3">角色名称
				<span class="required"> * </span>
			</label>
			<div class="col-md-8">
				<input type="text" name="name" value="<?= $model->name ?>" data-required="1" class="form-control" /> </div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">系统角色
				<span class="required"> * </span>
			</label>
			<div class="col-md-8">
				<select class="form-control" name="is_system">
					<option  value="0">否</option>
					<option <?php if($model->is_system): ?> selected <?php endif; ?> value="1">是</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-md-3 control-label">权限<span class="required"> * </span></label>
			<div class="col-md-8">
				<table cellspacing="0" cellpadding="0">
					<tbody>
					<?php if($access_list): ?>
						<?php foreach($access_list as $access): ?>
							<tr>
								<td style="width:100px;">
									<span title="<?= $access['name']?>"><?= $access['name']?></span>
								</td>
								<td style="width:80px;">
									全选
									<input  type="checkbox" <?php if(isset($role_access[$access['id']]) && count($role_access[$access['id']]) == count($access->moduleActions) ): ?> checked="checked" <?php endif; ?> name="role_group[]" class="check_all" value="<?= $access['id']?>">


								</td>
								<td>
									<?php if($actions = $access->moduleActions): ?>
										<?php foreach($actions as $action): ?>
									<label style="padding:5px;">
										<span title="<?= $action['action']?>"><?= $action['name']?></span>
										<input type="checkbox" <?php if($role_access && isset($role_access[$access['id']][$action['id']]) ): ?> checked="checked" <?php endif; ?>   class="node_item" name="role_access[]" value="<?= $access['id']?>_<?= $action['id']?>">
									</label>
										<?php endforeach; ?>
									<?php endif; ?>

								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>

		</div>

	</div>
	<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
	<div class="form-actions">
		<div class="row">
			<div class="col-md-offset-3 col-md-9">
				<button type="submit" class="btn green">确认权限</button>
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
		$('input').iCheck();
		var form1 = $('#role_form');
		form1.validate({
			errorElement: 'span', //default input error message container
			errorClass: 'help-block help-block-error', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			ignore: "",  // validate all fields including form hidden input
			messages: {
				name: {
					minlength: '至少2位字符',
					required: '必填项'
				},
				is_system: {
					required: '必填项'
				}
			},
			rules: {
				name: {
					minlength: 2,
					required: true
				},
				is_system: {
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


		//
//		$(".node_item").click(function(){
//			var _this = $(this);
//			var _par = _this.parents("tr");
//			var _allCheckStatus = true;
//			_par.find(".node_item").each(function(){
//				if(!$(this).is(":checked")){
//					_allCheckStatus = false;
//				}
//			});
//			if(_allCheckStatus){
//				_par.find(".check_all").prop("checked","checked");
//			}else{
//				_par.find(".check_all").removeAttr("checked");
//			}
//
//		});

		// all check
//		$(".check_all").click(function(){
//

//		});

		$('.check_all').on('ifClicked', function(event){
			var _this = $(this);
			var _par = _this.parents("tr");
			var bstop = $(this).is(":checked")?'uncheck':'check';
			_par.find(".node_item").each(function(){
				$(this).iCheck(bstop);
			});
		});

//		$('.check_all').on('ifUnchecked', function(event){
//			var _this = $(this);
//			var _par = _this.parents("tr");
//			_par.find(".node_item").each(function(){
//				$(this).iCheck('uncheck');
//			});
//		});

		$('.node_item').on('ifClicked', function(event){
			var bstop = $(this).is(":checked");
			var _this = $(this);
			var _par = _this.parents("tr");
			var _allCheckStatus =true;

			if(bstop){
				_par.find(".check_all").iCheck('uncheck');
			}
		});
	})

</script>