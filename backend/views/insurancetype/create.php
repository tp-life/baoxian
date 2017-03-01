<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
      type="text/css"/>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">商品</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>保险类型</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 保险类型
    <small>新增类型</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 添加完成后，均不能再进行修改 .</p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <form id="createInsuranceType" class="form-horizontal" method="post">
                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">

                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        您有一些错误,请检查您的输入
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        验证成功!
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">保险类型
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="type_name" placeholder="保险类型"/></div>
                            <span class="help-block"> </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">标识代码
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="type_code" value="<?= $code ?>"/></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">备注

                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <textarea rows="4" class="form-control" name="note"></textarea>
                            </div>
                        </div>
                    </div>
                    <p></p>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn green">确定</button>
                            </div>
                        </div>
                    </div>
            </form>
            <p></p>
        </div>
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    $(function () {

        var form2 = $('#createInsuranceType');
        var error2 = $('.alert-danger', form2);
        var success2 = $('.alert-success', form2);

        form2.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                type_name: {
                    required: true,
                    rangelength: [2, 25],
                },
                type_code: {
                    required: true,
                    number: true
                }
            },
            messages: {
                type_name: {
                    required: '请输入保险类型',
                    rangelength: '长度在于2到25位之间'
                },
                type_code: {
                    required: '请输入类型代码',
                    number: '请输入数字'
                }
            },

            invalidHandler: function (event, validator) {
//                success2.hide();
//                error2.show();
                showToastr('error','您有一些错误,请修正您的输入');
                App.scrollTo(error2, -200);
            },

            errorPlacement: function (error, element) {
                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                if (element.parents('.check_li').size() > 0) {
                    error.appendTo(element.parents('.check_li').attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }

            },

            highlight: function (element) {
                $(element)
                    .closest('.form-group').removeClass("has-success").addClass('has-error');
            },
            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            },

            submitHandler: function (form) {
//
//                success2.show();
//                error2.hide();
                var form_data = $(form).serializeArray();
                App.blockUI();
                $.post('<?=Yii::$app->urlManager->createUrl(['insurancetype/create'])?>', form_data, function (data) {
                    App.unblockUI();
                    data = typeof data == 'string' ? $.parseJSON(data) : data;
                    if (data.code !== 'yes') {
                        showToastr('error', data.message);
                        return false;
                    }
                    showToastr('success', data.message);
                    setTimeout(function () {
                        window.location.href = '<?=Yii::$app->urlManager->createUrl(['insurancetype/index'])?>';
                    }, 2000);
                });
                return false;
            }
        });
    })


</script>