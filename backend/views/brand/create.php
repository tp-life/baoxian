<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
      type="text/css"/>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">品牌管理</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>品牌型号</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 品牌管理
    <small>新增品牌型号</small>
</h3>

<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
<!--            <p> 添加完成后，保险公司名称、英文简称都不能再修改哦！ .</p>-->
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <!--                    <i class="icon-bubble font-green"></i>-->
                    <span class="caption-subject font-green bold uppercase">新增品牌型号</span>
                </div>
            </div>
            <form id="createBrandModel" class="form-horizontal" method="POST">

                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif ?>
                <input type="hidden" name="depth" value="<?= $depth ?>">
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
                        <label class="control-label col-md-3">所属上级
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <label><?=$brand?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">数据名称
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="model_name" value=""  />
                                <p class="help-block">
                                    支持批量处理同级数据。数据格式【三星#苹果#诺基亚】，即中间使用“#”分割。
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">排序
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="sort" value=""  />
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn green">保存</button>
                                &nbsp;&nbsp;
                                <button type="reset" class="btn">重置</button>
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
    jQuery(document).ready(function () {
        <?php $msg=Yii::$app->request->get('msg',null);if(!is_null($msg)){
       echo "showToastr('error','{$msg}','消息提示');";
   } ?>

            var form2 = $('#createBrandModel');
            var error2 = $('.alert-danger', form2);
            var success2 = $('.alert-success', form2);
            form2.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    model_name: {
                        required: true,
                        rangelength: [2, 50],
                    },

                },
                messages:{
                    company_name:{
                        required:'请输入型号名称'
                    }
                },

                invalidHandler: function (event, validator) {
                    //success2.hide();
                    //error2.show();
                    showToastr('error','您有一些错误,请修正您的输入');
//                    App.scrollTo(error2, -200);
                },

                errorPlacement: function (error, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                    if (element.parents('.check_li').size() > 0) {
                        error.appendTo(element.parents('.check_li').attr("data-error-container"));
                    }else{
                        error.insertAfter(element);
                    }

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

                    var form_data=$(form).serializeArray();
                    App.blockUI();
                    $.post('<?=Yii::$app->urlManager->createUrl(['brand/create'])?>',form_data,function(data){
                        App.unblockUI();
                        data=typeof data =='string'? $.parseJSON(data):data;
                        if(data.code !=='yes'){
                            showToastr('error',data.message);
                            return false;
                        }
                        showToastr('success',data.message);
                        setTimeout(function(){
                            window.location.href='<?=Yii::$app->urlManager->createUrl(['brand/index'])?>';
                        },2000);
                    });

                    return false;
                }
            });

    });
</script>