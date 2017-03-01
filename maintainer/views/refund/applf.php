

<div class="page-bar"></div>
<h3 class="page-title"> 卡券退款
</h3>
<div class="row">
    <div class="col-md-12">
        <!--        <div class="note note-danger">-->
        <!--            <p> 卡券将直接发放至商家.</p>-->
        <!--        </div>-->
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">

            <form id="refund_form" class="form-horizontal" method="post" enctype="multipart/form-data" method="POST">

                <input type="hidden" name="_csrf-maintainer" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="order_id" value="<?=$order_id?>">
                <div class="form-body">
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">退款险种</label>
                        <div class="col-md-4">
                            <div class="input-icon right">

                                <label class="control-label font-purple-seance sbold"><?=$model->coverage_code?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">退款卡号</label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <textarea class="form-control" rows="5" name="card_number_str" id="card_number_str"></textarea>
                            </div>
                            <span class="help-block font-red-pink">单个卡券用","分开</span>
                            <span>
								或者&nbsp;<?= \common\widgets\Parsefile::widget(['id'=>'card_number_str']) ?>
							</span>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">退款备注</label>
                        <div class="col-md-4">
                            <div class="input-icon right">
                                <textarea class="form-control" rows="2" name="card_remark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-right">当前退款卡券数量：<span id="refund_card_num" class="font-red-mint">0</span> 张</p>
                            </div>
                            <div class=" col-md-3">
                                <button type="button" id="submitBtn" class="btn green">确定</button>
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

<!-- END PAGE LEVEL PLUGINS -->

<script>
$(function(){
    $('textarea[name="card_number_str"]').on('input',function(){
        var text = $(this).val();
        var peg =/[,，]+/gi;
        var new_text=text.replace(peg,',');
        var text_arr = new_text.split(',');
        var l =text_arr.length;
        if(text_arr[l-1] == ''){
            l = l-1;
        }
        var size = l;
        for(var i =0;i<l;i++ ){
            if(text_arr[i].indexOf('|') != -1){
                var tmp = text_arr[i].split('|');
                if(tmp[1] > tmp[0]){
                    size += parseInt(tmp[1] - tmp[0]);
                }
            }
        }
        if(size > 0){
            $('#refund_card_num').text(size);
        }
    });

    $('#submitBtn').on('click',function(){
        var data= $('#refund_form').serialize();
        $.post('<?=Yii::$app->urlManager->createUrl('refund/applf')?>',data,function(data){
            if(data.code =='yes'){
                showToastr('success',data.message);
                setTimeout(function(){
                    location.href='<?=Yii::$app->urlManager->createUrl('card/index')?>';
                },1500);
            }else{
                bootbox.alert('<span class="font-purple-seance" style="font-size: 14px;">'+data.message+'</span>');
            }
        });
    });
});
</script>