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
            <span>保险公司</span>
        </li>
    </ul>
</div>
<h3 class="page-title"> 保险公司
    <small><?=$info['id']?'编辑保险公司信息':'新增保险公司'?></small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 添加完成后，保险公司名称最好不要修改哦！.</p>
			<p> 添加完成后，保险公司英文简称都不能再修改哦！ .</p>
			<p>保险公司英文简称将作为保险险种代码算法一部分！</p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <!--                    <i class="icon-bubble font-green"></i>-->
                    <span class="caption-subject font-green bold uppercase"><?=$info['id']?'编辑保险公司信息':'新增保险公司'?></span>
                </div>
            </div>
            <form id="createInsuranceCompany" class="form-horizontal" method="post" enctype="multipart/form-data"
                  method="POST">

                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
                <?php if ($info['id']): ?>
                    <input type="hidden" name="id" value="<?= $info['id'] ?>">
                <?php endif ?>
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
                        <label class="control-label col-md-3">保险公司名称
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $info['name'] ?>"
                                       name="company_name" <?= $info['id'] ? '' : '' ?> /></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">英文简称
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $info['sp'] ?>"
                                       name="sp" <?= $info['id'] ? 'readonly' : '' ?> /></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">联系人
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="concat_name"
                                       value="<?= $info['contact_name'] ?>"
                                       placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">联系电话
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="concat_tel"
                                       value="<?= $info['contact_phone'] ?>" placeholder=""/></div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">所在地址
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-5">
                            <div class="input-icon right">
                                <select class="form-control input-small input-inline" name="p_id"
                                        id="province_id">
                                    <option value="">请选择省</option>
                                    <?php foreach ($province as $key => $val): ?>
                                        <option
                                            value="<?= $val->area_id . ',' . $val->area_name ?>" <?= $val->area_id == $info['p_id'] ? 'selected' : '' ?>><?= $val->area_name ?></option>
                                    <?php endforeach ?>
                                </select>

                                <select class="form-control input-small input-inline" name="c_id" id="city_id"
                                        style="display: <?= $city_html ? 'inline' : 'none' ?>">
                                    <?= $city_html ?>
                                </select>
                                <select class="form-control input-small input-inline" name="a_id" id="area_id"
                                        style="display: <?= $area_html ? 'inline' : 'none' ?>">
                                    <?= $area_html ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">

                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control input-xlarge"
                                       value="<?= $info['address_detail'] ?>" name="address_detail" placeholder=""/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">公司logo
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <div>
                                    <input type="hidden" name="logo" id="logo" value="<?= $info['logo'] ?>">
<!--                                    <input type="file" name="logo" id="fileToUpload" style="display: none;">-->
                                    <img width="120" height="120" id="uploadPic" class="show_img"
                                         style="border: 1px solid #cccccc;cursor: pointer"
                                         src="<?= $info['logo'] ? $info['logo'] : Yii::getAlias('@image') . '/upload.png' ?>">

                                    <span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
                                </div>
                               <div class="col-md-3" style="margin-top: 5px;">
                                   <?=\common\widgets\Upload::widget(['id'=>'logo','img'=>'uploadPic','name'=>'company'])?>
                               </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">备注

                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <textarea class="form-control" rows="5" name="note"><?= $info['note'] ?></textarea>
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
<script src="<?= Yii::getAlias('@js'); ?>/ajaxfileupload.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/create-company.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function () {
        <?php $msg=Yii::$app->request->get('msg',null);if(!is_null($msg)){
       echo "showToastr('error','{$msg}','消息提示');";
   } ?>


    });
</script>