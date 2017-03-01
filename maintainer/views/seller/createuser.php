
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 新增用户,帐号必须为手机号码 .</p>
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
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="name" placeholder="用户手机号码" /> </div>
                            <span class="help-block"> </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">登录密码
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="password" class="form-control" name="password" /> </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn green">下一步</button>
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
                name: {
                    required: true,
                    digits: true,
                    rangelength: [11, 11],
                    remote: {
                        url: '<?=Yii::$app->urlManager->createUrl('seller/check')?>',
                        type: 'get',
                        data: {
                            phone: function () {
                                return $('#createSeller input[name="name"]').val();
                            }
                        }
                    }
                },
                password: {
                    required: true,
                    rangelength: [6, 18]
                },
            },
            messages:{
                name:{
                    required:'请输入手机号码',
                    remote:'该号码已经被使用,请换个号码继续注册'
                },
                password:{
                    required:'请输入密码'
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
                submitFrom(form_data,'<?=Yii::$app->urlManager->createUrl('seller/createuser')?>');
                return false;

            }
        });
    })


</script>