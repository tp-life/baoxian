<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">卡券发放处理</h4>
</div>
<div class="modal-body">
    <div class="row">
        <form action="#" id="service_form" method="POST" class="form-horizontal">
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-2">
                    </label>
                    <div class="col-md-6">
                        <table class="table table-bordered  table-hover font-red">
                            <thead>
                            <th>发放险种</th>
                            <th>险种名称</th>
                            <th>发放数量</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $coverage_model['coverage_code'] ?></td>
                                <td><?= $coverage_model['coverage_name'] ?></td>
                                <td><?= $count ?></td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">
                        发放商家<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">
						<?= \yii\helpers\Html::dropDownList('to_seller_id','',$seller_data,['class'=>'form-control form-filter','id'=>'to_seller_id']) ?>
                        <br/>
                        <font style="margin: 0px 1px 0px 0px;font-size: small" class="font-purple-medium">默认 合作状态中所有子商家</font>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">卡券编号<span class="required"> * </span></label>
                    <div class="col-md-6">
                        <div class="input-icon right">
                            <textarea class="form-control" readonly rows="5" id="card_number_str" name="card_number_str"><?= $card_number_str; ?></textarea>
                        </div>
                        <span class="help-block font-red-pink">文本框内多个卡券用","分开,每张卡券号由7位数字构成</span>

                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">发放备注
                        <span class="required">* </span>
                    </label>
                    <div class="col-md-6">
                        <textarea name="card_remark" id="card_remark" value=""  class="form-control" placeholder="请输入简要的备注信息" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <input type="hidden" name="_csrf-maintainer" value="<?=Yii::$app->request->csrfToken?>">
            <input type="hidden" name="d_coverage" value="<?= $coverage_model['coverage_code'] ?>">
            <input type="hidden" name="card_num" value="<?= $count ?>">
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 ">
                        <button type="button" class="btn green" id="submit_ffzsj" >发放卡券</button>
                    </div>
                </div>
            </div>
        </form>


    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">

    $(function(){

        $('#submit_ffzsj').on('click',function(){
            var is_ok = true;
            var kay_value_set = $('#service_form').serializeArray();
            $(kay_value_set).each(function(key,item){
                if(item.name=='card_remark' && item.value==''){
                    showToastr('error', '填写发放备注');
                    is_ok = false;
                }
            });
            if(!is_ok){
                return false;
            }
            bootbox.confirm('<span class="font-green-jungle font-lg sbold">确认发放给选中商家？</span>', function (result) {
                if (result) {
                    App.startPageLoading();
                    $.post(
                        '<?= \yii\helpers\Url::to(['card/grant']) ?>',
                        kay_value_set,
                        function(e){
                            App.stopPageLoading();
                            console.log(e);
                            if(e.code == 'yes'){
                                showToastr('success', e.message,'','toast-top-right');
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }else{
                                showToastr('error', e.message,'','toast-top-right');
                            }
                        },
                        'json'
                    );
                }
            });




        });

    });

</script>