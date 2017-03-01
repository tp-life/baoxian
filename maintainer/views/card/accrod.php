<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
      type="text/css"/>

<div class="page-bar"></div>
<h3 class="page-title"> 卡券发放
</h3>
<div class="row">
    <div class="col-md-12">
<!--        <div class="note note-danger">-->
<!--            <p> 卡券将直接发放至商家.</p>-->
<!--        </div>-->
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">

            <form id="merge_form" class="form-horizontal" method="post" enctype="multipart/form-data" method="POST">

                <input type="hidden" name="_csrf-maintainer" value="<?= Yii::$app->request->csrfToken ?>">
                <?php if($info['id']): ?>
                    <input type="hidden" name="id" value="<?=$info['id']?>">
                <?php endif ?>
                <div class="form-body">
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">发放至商家
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
<!--                            <div class="input-group">-->
<!--                                <input type="text" class="form-control" id="to_check_val">-->
<!--                                    <span class="input-group-btn">-->
<!--                                        <button class="btn blue" id="to_check" type="button">过滤</button>-->
<!--                                    </span>-->
<!--                            </div>-->
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select class="form-control" name="to_seller_id" id="to_seller_id">
                                    <option value="">选择保险商家</option>
                                    <?php if($insurance_list){ ?>
                                        <?php foreach($insurance_list as $vo): ?>
                                            <option value="<?= $vo['seller_id']; ?>"><?= $vo['seller_name']; ?></option>
                                        <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">待合并险种
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select class="form-control" name="d_coverage" id="d_coverage">
                                    <option value="">请选择待合并险种</option>
                                    <?php foreach($code_list as $val): ?>
                                        <option value="<?=$val['coverage_code']?>" ><?=$val['coverage_code']?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">发放卡券数量 <span class="required"> * </span></label>
                        <div class="col-md-4">
                            <input  class="form-control " name="card_num">

                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">填写合并套餐编号</label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <textarea class="form-control" rows="5" id="card_number_str" name="card_number_str"></textarea>
                            </div>
                            <span class="help-block font-red-pink">文本框内多个卡券用","分开,每张卡券号由7位数字构成</span>
							<span>
								或者&nbsp;<?= \common\widgets\Parsefile::widget(['id'=>'card_number_str']) ?>
							</span>
                        </div>
                    </div>
                <div class="form-group  margin-top-20">
                    <label class="control-label col-md-3">客服备注</label>
                    <div class="col-md-4">
                        <div class="input-icon right">
                            <textarea class="form-control" rows="2" name="card_remark"></textarea>
                        </div>
                    </div>
                </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="button" id="submitBtn" class="btn green">保存</button>
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

<script>
    $(function () {
        //合并目标商家过滤
        $('#to_check').on('click', function () {
            var seller_name = $('#to_check_val').val();
            var str = '<option value="">选择保险商家</option>';
            $.post('/card/getseller', {
                    'seller_name': seller_name,
                    'type' : 'insurance',
                    '_csrf-maintainer': $('meta[name="csrf-token"]').attr("content")
                },
                function (data) {
                    if (data.code == 'yes') {
                        $.each(data.data, function (index, val) {
                            str += '<option value=' + val.seller_id + '>' + val.seller_name + '</option>';
                        })
                    }
                    $('#to_seller_id').html(str);
                }, "json");
        });
    });

    $("#merge_form").validate({
        rules: {
            to_seller_id: {
                required: true
            },
            d_coverage:{
                required:true
            },
			card_num:{
				required:true
			}
        },
        messages: {
            to_seller_id: {
                required: '<b style="color: red">请选择商家<b/>'
            },
            d_coverage:{
                required: '<b style="color: red">请选择发放的险种<b/>'
            },
			card_num:{
				required: '<b style="color: red">请填写发放卡券数量<b/>'
			}
        }
    });
    $("#submitBtn").click(function () {
        if ($("#merge_form").valid()) {
            var from_data=$("#merge_form").serialize();
            App.blockUI();
            $.post('<?=Yii::$app->urlManager->createUrl('card/grant')?>',from_data,function(data){
                App.unblockUI();
                if(data.code == 'yes'){
                    showToastr('success',data.message);
                    setTimeout(function(){
                        window.location.reload();
                    },1500);
                }else{
                    showToastr('error',data.message);
                    return false;
                }
            });
        }

    });
</script>