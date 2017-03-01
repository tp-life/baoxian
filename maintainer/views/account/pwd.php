
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p>修改密码.</p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <form action="/user/create" id="createSeller" class="form-horizontal" method="post">
                <input type="hidden" name="_csrf-maintainer" value="<?=Yii::$app->request->csrfToken?>">
                <div class="form-body">
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">用户帐号
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <label class="control-label"><?= Yii::$app->user->identity->name ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">原始密码
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="password" class="form-control" name="password" /> </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">新密码
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="password" class="form-control" name="new_pwd" id="new_pwd" /> </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">确认密码
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="password" class="form-control" name="sure_pwd" /> </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn green">确认修改</button>
                            </div>
                        </div>
                    </div>
            </form>
            <p></p>
        </div>
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    $(function(){
        <?php if(isset($msg)){
        echo "showToastr('error','{$msg}','消息提示');";
    } ?>

        var form2 = $('#createSeller');

        form2.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                password: {
                    required: true,
                    rangelength: [6, 18]
                },
                new_pwd: {
                    required: true,
                    rangelength: [6, 18]
                },
                sure_pwd:{
                    equalTo:"#new_pwd"
                }
            },
            messages:{

                password:{
                    required:'请输入原始密码'
                },
                new_pwd:{
                    required:'请输入新密码'
                },
                sure_pwd:{
                    equalTo:"两次密码不一致"
                }
            },

            invalidHandler: function (event, validator) {
                showToastr('error','您有一些错误,请修正您的输入');
            },

            errorPlacement: function (error, element) {
                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                error.insertAfter(element);
            },

            highlight: function (element) {
                $(element)
                    .closest('.form-group').removeClass("has-success").addClass('has-error');
            },
            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            submitHandler: function (form) {
                var form_data = $(form).serializeArray();
                submitFrom(form_data,'<?=Yii::$app->urlManager->createUrl('account/pwd')?>',location.href);
                return false;

            }
        });
    })


</script>