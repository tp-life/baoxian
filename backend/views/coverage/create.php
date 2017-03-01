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
            <span>保险商品</span>
        </li>
    </ul>
</div>
<h3 class="page-title"> 保险商品
    <small><?=$info['id']?'编辑':'新增'?>保险</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p>• 添加完成后，保险公司、官方指导价、保险类型和保期不能进行修改</p>
            <p>• 保险险种编码为自动生成</p>
            <p>• 险种生成规则：保险公司英文代码+保期（两位数字）+官方指导价+保险类型识别代码 如:<span style="color: red">锦泰一年期188套餐碎屏险，险种为：JT1218801</span></p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <!--                    <i class="icon-bubble font-green"></i>-->
                    <span class="caption-subject font-green bold uppercase"><?=$info['id']?'编辑':'新增'?>保险</span>
                </div>
            </div>
            <form id="createInsuranceCoverage" class="form-horizontal" method="post" enctype="multipart/form-data" method="POST" action="<?=Yii::$app->urlManager->createUrl(['coverage/update'])?>">

                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
                <?php if($info['id']): ?>
                    <input type="hidden" name="id" value="<?=$info['id']?>">
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
                        <label class="control-label col-md-3">保险名称
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $info['coverage_name'] ?>"
                                       name="coverage_name" /></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">保险公司
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select name="company_name"  class="form-control" id="company_id" onchange="checkCoverage()" <?=$info['id']?'disabled':''?> >
                                    <option value="">请选择所属公司</option>
                                    <?php if($model_company):foreach($model_company as $val): ?>
                                        <option value="<?=$val->id.','.$val->name.','.$val->sp?>" <?=$info['company_id']==$val->id?'selected':''?> ><?=$val->name?></option>
                                    <?php endforeach;endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">保险类型
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select name="type_name"  class="form-control" id="type_id" onchange="checkCoverage()" <?=$info['id']?'disabled':''?> >
                                    <option value="">请选择所属类型</option>
                                    <?php if($model_type):foreach($model_type as $val): ?>
                                        <option value="<?=$val->id.','.$val->type_name.','.$val->type_code?>" <?=$info['type_id']==$val->id?'selected':''?> ><?=$val->type_name?></option>
                                    <?php endforeach;endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">保险保期
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select name="period"  class="form-control" id="period" onchange="checkCoverage()" <?=$info['id']?'disabled':''?> >
                                    <option value="">请选择保期</option>
                                    <option value="1" <?=$info['period']==1?'selected':''?>> 1 月</option>
                                    <option value="2" <?=$info['period']==2?'selected':''?>> 2 月</option>
                                    <option value="3" <?=$info['period']==3?'selected':''?>> 3 月</option>
                                    <option value="6" <?=$info['period']==6?'selected':''?>> 6 月</option>
                                    <option value="12" <?=$info['period']==12?'selected':''?>> 12 月</option>
                                    <option value="18" <?=$info['period']==18?'selected':''?>> 18 月</option>
                                    <option value="24" <?=$info['period']==24?'selected':''?>> 24 月</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">是否多次维保
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="radio-list">

                                <label class="radio-inline">
                                    <input type="radio" value="1"  name="is_more" <?=$info['is_more']==1?'checked="checked" readonliy ':''?> > 是 </label>

                                <label class="radio-inline">
                                    <input type="radio" value="0" name="is_more" <?=!$info['is_more']?'checked="checked" readonliy ':''?> /> 否 </label>
                            </div>

                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">成本价
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="cost_price" value="<?= $info['cost_price'] ?>"
                                       placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">批发价
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="wholesale_price"
                                       value="<?= $info['wholesale_price'] ?>" placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">官方指导价
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" id="official_price" name="official_price" value="<?= $info['official_price'] ?>"
                                    <?=$info['id']?'disabled':''?> /></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">最高赔付
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="max_payment"
                                       value="<?= $info['max_payment'] ?>" placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3 red">保险险种
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="coverage_code" id="coverage_code"
                                       value="<?= $info['coverage_code'] ?>" placeholder="" readonly="readonly" /></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">保险图片
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <div >
                                    <input type="hidden" name="image" id="coverage_image" value="<?=$info['image']?>"  >
<!--                                    <input type="file" name="image" id="fileToUpload" style="display: none;">-->
                                    <img width="120" height="120" class="show_img" id="uploadPic" style="border: 1px solid #cccccc;cursor: pointer" src="<?=$info['image']?$info['image']:Yii::getAlias('@image').'/upload.png'?>">

                                    <span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
                                </div>
                                <div class="col-md-3" style="margin-top: 5px;">
                                    <?=\common\widgets\Upload::widget(['id'=>'coverage_image','img'=>'uploadPic','name'=>'coverage'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">保险说明

                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <textarea class="form-control" rows="5" name="note"><?=$info['note']?></textarea>
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
<script src="<?= Yii::getAlias('@js'); ?>/create-coverage.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script type="text/javascript">
    jQuery(document).ready(function () {
        <?php $msg=Yii::$app->request->get('msg',null);if(!is_null($msg)){
       echo "showToastr('error','{$msg}','消息提示');";
   } ?>
    });
	$(function(){

		$('#uploadPic').on('click',function(){
//			$('#fileToUpload').click();
		});
		$('#fileToUpload').on('change',function(){
			var src = getFullPath($(this)[0]);
			$('#uploadPic').attr('src',src);
			$.ajaxFileUpload({
				url:'<?= \yii\helpers\Url::to(['coverage/upload']) ?>',
				secureuri :false,
				fileElementId :'fileToUpload',
				data:{'_csrf-backend':$('meta[name="csrf-token"]').attr("content")},
				dataType : 'json',
				success : function (data){

					if(typeof(data.status) != 'undefined'){
						if(data.status == 1){
							showToastr('success',data.message);
							$('#coverage_image').val(data.url);
						}else{
							showToastr('error',data.msg);
							$('#uploadPic').attr('src','');
						}
					}
				},
				error: function(data, status, e){
					showToastr('error',e);
				}
			});

		});


	})

</script>