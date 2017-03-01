<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">帐号管理</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>添加子商户</span>
        </li>
    </ul>

</div>
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
                            &nbsp;&nbsp;
                            <a style="display: none" id="nextStep" class="btn blue">当前用户已存在,是否继续完善信息?</a>
                        </div>
                    </div>
                </div>
            </form>
            <p></p>
        </div>
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/form-validation.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    $(function(){
        <?php if(isset($msg)){
        echo "showToastr('error','{$msg}','消息提示');";
    } ?>
    })


</script>