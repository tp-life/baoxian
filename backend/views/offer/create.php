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
            <span>维修报价</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 品牌管理
    <small><?=$info['id']?'编辑':'新增'?>维修报价</small>
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
                    <span class="caption-subject font-green bold uppercase"><?=$info['id']?'编辑':'新增'?>维修报价</span>
                </div>
            </div>
            <form id="createBrandModel" class="form-horizontal" method="POST">

                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
                <?php if ($info): ?>
                    <input type="hidden" name="id" value="<?= $info['offer_id'] ?>">
                <?php endif ?>
                <div class="form-body">
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">品牌
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <select class="form-control" name="brand_id"
                                        id="brand_id">
                                    <option value="">请选择品牌</option>
                                    <?php foreach ($brand as $key => $val): ?>
                                        <option
                                            value="<?= $val->id . ',' . $val->model_name ?>" <?= $val->id == $info['brand_id'] ? 'selected' : '' ?>><?= $val->model_name ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20" style="display: <?= $model_html ? 'block' : 'none' ?>" id="model_c">
                        <label class="control-label col-md-3">型号
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <select class="form-control" name="model_id" id="model_id">
                                    <?= $model_html ?>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20" style="display: <?= $color_html ? 'block' : 'none' ?>" id="color_c">
                        <label class="control-label col-md-3">机型颜色
                            <span class="required"> </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <select class="form-control" name="color_id" id="color_id">
                                    <?=$color_html?>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">内屏报价
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="inner_screen" value="<?=$info['inner_screen']?>"  />

                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">外屏报价
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="outer_screen" value="<?=$info['outer_screen']?>"  />

                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">平台服务费
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="commission" value="<?=$info['commission']?>" placeholder="默认5%"  />
                                <em class="help-block">平台收取的服务费用,默认为5%.请直接填写数字即可..</em>
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
            form2.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    model_id:{
                        required:true
                    },
                    inner_screen:{
                        required:true,
                        number: true,
                        range: [0, 9999]
                    },
                    outer_screen:{
                        required:true,
                        number: true,
                        range: [1, 9999]
                    },
                    commission:{
                        digits: true,
                        range: [0, 100]
                    }

                },
                messages:{
                    model_id:{
                        required:'请选择型号'
                    },
                    inner_screen:{
                        required:'请填写内屏报价',
                        number: '请填写有效的数字',
                        range: '请填写[0-9999]之间的数字'
                    },
                    outer_screen:{
                        required:'请填写外屏报价',
                        number: '请填写有效的数字',
                        range: '请填写[1-9999]之间的数字'
                    },
                    commission:{
                        digits: '请填写整数',
                        range: '请填写1到100之间的数字'
                    }
                },

                invalidHandler: function (event, validator) {
                    showToastr('error','您有一些错误,请修正您的输入');
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
                    $.post('<?=Yii::$app->urlManager->createUrl(['offer/create'])?>',form_data,function(data){
                        App.unblockUI();
                        data=typeof data =='string'? $.parseJSON(data):data;
                        if(data.code !=='yes'){
                            showToastr('error',data.message);
                            return false;
                        }
                        showToastr('success',data.message);
                        setTimeout(function(){
                            window.location.href='<?=Yii::$app->urlManager->createUrl(['offer/index'])?>';
                        },2000);
                    });

                    return false;
                }
            });

        $('#brand_id , #model_id').on('change', function () {
            var province = $(this).val();
			if(province == ''){
				return ;
			}
            var pval = province.split(',');
            var name = this.name;
            $.post('<?=Yii::$app->urlManager->createUrl('offer/getbrand')?>', {
                'id': pval[0],
                '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
            }, function (data) {
                data = typeof data == 'string' ? $.parseJSON(data) : data;
                if (data.code !== 'yes') {
                    showToastr('warning', data.message);
                    return false;
                }

                var title=name == 'brand_id'?'请选择型号':'请选择颜色';
                var html = '<option value="">'+title+'</option>';
				var html = '';
                $.each(data.data, function (index, ele) {
                    html += '<option value="' + ele.id + ',' + ele.model_name + '">' + ele.model_name + '</option>';
                })
                if(data.data.length  < 1 ){
                    if (name == 'brand_id') {
                        $('#model_c').css('display', 'none');
                        $('#color_c').css('display', 'none');
                    }else if(name == 'model_id'){
                        $('#color_c').css('display', 'none');
                    }
                    return false;
                }
                if (name == 'brand_id') {
                    $('#model_id').html(html);
                    $('#model_c').css('display', 'block');
                    $('#color_c').css('display', 'none');
                } else if (name == 'model_id') {
                    if(data.data.length > 0){
                        $('#color_id').html(html);
                        $('#color_c').css('display', 'block');
                    }

                }
            });
        });

    });


</script>