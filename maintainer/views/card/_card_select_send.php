<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">申领信息发放处理</h4>
</div>
<div class="modal-body">
    <div class="row">
        <form action="#" id="service_form" method="POST" class="form-horizontal">
            <div class="form-body">
                <div class="form-group">
                    <label class="control-label col-md-2">
                        申领商家<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">
                        <input class="form-control" readonly="" value="测试子帐号2 18080092223" placeholder="" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">
                        申领货号<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">
                        <input class="form-control" readonly="" value="750533745265011948" placeholder="" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">
                        付款方式<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">
                        <input class="form-control" readonly="" value=" 部分付款" placeholder="" type="text">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">
                        险种名称<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">

                        <input class="form-control" readonly="" value="半年包摔险" placeholder="" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">
                        险种编码<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">

                        <input class="form-control" readonly="" value="wn0616820" placeholder="" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">
                        险种数量<span class="required"> * </span>
                    </label>
                    <div class="col-md-6">

                        <input class="form-control" readonly="" value="2" placeholder="" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">卡券编号<span class="required"> * </span></label>
                    <div class="col-md-6">
                        <div class="input-icon right">
                            <textarea class="form-control" rows="5" id="card_number_str" name="card_number_str"></textarea>
                        </div>
                        <span class="help-block font-red-pink">文本框内多个卡券用","分开,每张卡券号由7位数字构成</span>
                        <span>
								或者&nbsp;<a class="parsefile btn" title="点击转化处理">导入转化</a>
&nbsp;
<a href="javascript:;" class="btn" onclick="sayParseDemo()" title="查看demo">查看demo</a>
<div style="display: none;">
	<input style="display: none;" name="UploadForm[file]" id="file_parse" type="file">
</div>
<script src="http://sh.baoxian.com/static/js/ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript">

	$('.parsefile').on('click', function () {
        $('#file_parse').click();
    });

    $('#file_parse').on('change', function () {

        $.ajaxFileUpload({
            url: '/mimport/default/upload',
            secureuri: false,
            fileElementId: 'file_parse',
            data: {
                '_csrf-maintainer':'OFFicFg0OUgVEBE9AVNgKQkEBjsIBAg.VDYYRRR4XSt5GydGNUBMEA==',
                '_csrf-backend':'OFFicFg0OUgVEBE9AVNgKQkEBjsIBAg.VDYYRRR4XSt5GydGNUBMEA=='
            },
            dataType: 'json',
            success: function (data,status) {
                //console.log(data);
                //console.log(status);
                //console.log(data.code);
                if(data.code=='yes'){
                    $('#card_number_str').val(data.data.cards);
                    showToastr('success',data.message);
                    bootbox.alert('本次卡券共计：'+data.data.count, function() {
                    });
                }else{
                    showToastr('error',data.message);
                }

            },
            error: function (data, status, e) {
                showToastr('error', e);
            }
        })

    });

    function sayParseDemo()
    {
        var demo ="<div class=\"bg-grey-salsa bg-font-grey-salsa\">序列号1<br/>" +
            "序列号2<br/>" +
            "序列号3<br/>" +
            "序列号4<br/>" +
            "序列号5<br/>" +
            "---</div>";

        bootbox.dialog({
            message:demo,
            size:'large',
            title:'文件转化Demo'
        });
    }

</script>							</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-2">发放备注
                        <span class="required">* </span>
                    </label>
                    <div class="col-md-6">
                        <textarea name="service_note" id="service_note" value="" class="form-control" placeholder="请输入简要的备注信息" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <input name="order_id" value="31" type="hidden">
            <input name="_csrf-maintainer" value="OFFicFg0OUgVEBE9AVNgKQkEBjsIBAg.VDYYRRR4XSt5GydGNUBMEA==" type="hidden">
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="button" class="btn green" id="submit_lipei_liucheng">Submit</button>
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

        $('#submit_lipei_liucheng').on('click',function(){

            var ok = true;
            var kay_value_set = $('#service_form').serializeArray();
            $(kay_value_set).each(function(key,item){
                if(item.name=='card_number_str' && item.value==''){
                    showToastr('error', '填写待发放卡券序列号');
                    ok = false;
                }
                if(item.name=='service_note' && item.value==''){
                    showToastr('error', '填写简要备注信息');
                    ok = false;
                }
            });
            if(!ok){
                return false;
            }
            App.startPageLoading();
            $.post(
                '/card/issuemod',
                kay_value_set,
                function(e){
                    console.log(e);
                    if(e.code == 'yes'){
                        showToastr('success', e.message,'','toast-top-right');
                        window.location.reload();
                    }else{
                        showToastr('error', e.message,'','toast-top-right');
                    }
                },
                'json'

            );
            App.stopPageLoading();

        });

    });

</script>