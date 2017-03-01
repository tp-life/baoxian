
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">卡券回款列表</span>
                </div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-search text-right">
                                        <select
                                            class="table-group-action-input form-control form-filter input-inline  input-sm"
                                            id="datatable_filter" name="status">
                                            <option value="">订单状态</option>
                                            <option value="1">待回款</option>
                                            <option value="2">部分回款</option>
                                            <option value="3">全部回款</option>
                                            <option value="4">已取消</option>
                                        </select>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入商家名称进行查询"/>
                                        <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                            <i class="fa fa-search"></i> 搜索
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 商家名称</th>
                            <th width="8%">  卡券订单号</th>
                            <th width="8%">  申请卡券数量</th>
                            <th width="8%"> 卡券总价值</th>
                            <th width="8%"> 退卡金额</th>
                            <th width="8%"> 待收金额</th>
                            <th width="8%"> 实收金额</th>
                            <th width="8%"> 订单状态</th>
                            <th width="10%">  申请时间</th>
                            <th width="18%"> 操作</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div style="display: block">

            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<!-- begin 协议 -->
<div class="modal fade bs-modal-lg" tabindex="-1" id="my-card-apply" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<img src="<?= Yii::getAlias('@metro') ?>/global/img/loading-spinner-grey.gif" alt="" class="loading">
				<span> &nbsp;&nbsp;Loading... </span>
			</div>
		</div>
	</div>
</div>
<!-- end 协议 modal -->
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('finance/getdata')?>', {}, false, 'datatable_ajax');
    });


    function handleMoney(pay_id,price,s_price,status,y,w,t){
        var check_1='';
        var check_2='';
        var check_3='';
        if(status == 0){
            check_1='checked';
        }else if(status == 2){
            check_2 ='checked';
        }else if(status == 3){
            check_3 ='checked';
        }

        var html ='<form id="receiptFinance" class="form-horizontal" method="post">'+
            '<div class="form-body">'+
            '<div class="form-group   margin-top-10">'+
            '<label class="control-label col-md-3">回款状态：</label>'+
            '<div class="col-md-4">'+
            '<div class="radio-list col-sm-12"><label class="radio-inline"><input type="radio" name="pay_status" '+check_1+' value="1"> 未回款</label>&nbsp;&nbsp;&nbsp;'+
            '<label class="radio-inline"><input type="radio"  name="pay_status" '+check_2+' value="2"> 部分回款</label>&nbsp;&nbsp;&nbsp;'+
            '<label class="radio-inline"><input type="radio" name="pay_status" '+check_3+' value="3"> 全部回款</label></div></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">卡券总价值：</label>'+
            '<div class="col-md-5">'+
            '<label id="issue_coverage_name" class="font-purple-seance control-label">¥ '+y+'  </label><span class="font-red-flamingo">&nbsp;&nbsp;&nbsp;&nbsp;( 已发放卡券金额 + 未发放卡券金额 )</span></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">已发放卡券金额：</label>'+
            '<div class="col-md-4">'+
            '<label id="issue_coverage_name" class="font-purple-seance control-label">¥ '+(y-w)+'</label></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">未发放卡券金额：</label>'+
            '<div class="col-md-4">'+
            '<label id="issue_coverage_name" class="font-purple-seance control-label">¥ '+w+'</label></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">退卡总金额：</label>'+
            '<div class="col-md-4">'+
            '<label id="issue_coverage_name" class="font-purple-seance control-label">¥ '+t+'</label></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">应收金额：</label>'+
            '<div class="col-md-5">'+
            '<label id="issue_coverage_name" class="font-purple-seance control-label">¥ '+price+'</label><span class="font-red-flamingo">&nbsp;&nbsp;&nbsp;&nbsp;( 卡券总价值 - 退卡总金额 -  已收金额 )</span></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">已收金额：</label>'+
            '<div class="col-md-4">'+
            '<label id="issue_coverage_name" class="font-purple-seance control-label">¥ '+s_price+'</label></div>'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="control-label col-md-3">实收金额（元）：</label>'+
            '<div class="col-md-4"><div class="input-icon"><i class="fa fa-jpy"></i>'+
            '<input type="text" name="actual" class="form-control" value="'+s_price+'" ></div></div>'+
            '</div>'+
            '</form>';
        bootbox.dialog({
            message:html,
            size:'large',
            title:'卡券回款管理',
            buttons: {
                confirm: {
                    label: '确定',
                    className: 'btn-success',
                    callback:function(result){
                        var actual = $('#receiptFinance input[name="actual"]').val();
                        var pay_status = $('#receiptFinance input[name="pay_status"]:checked').val();
                        var reg=/^[0-9]+(.[0-9]{2})?/;
                        if(!reg.test(actual)){
                            showToastr('error','实收金额请填写有效数字');
                            return false;
                        }
                        var parms={
                            pay_id:pay_id,
                            pay_status:pay_status,
                            actual:actual,
                            '_csrf-backend':'<?=Yii::$app->request->csrfToken?>'
                        };
                        App.blockUI();
                        $.post('<?=Yii::$app->urlManager->createUrl('finance/receipt')?>',parms,function(data){

                            App.unblockUI();
                            if(data.code == 'yes'){
                                showToastr('success',data.message);
                                setTimeout(function(){
                                    window.location.reload();
                                },1500);
                            }else{
                                showToastr('error',data.message);
                            }
                            return false;

                        });

                        return false;
                    }
                },
                cancel: {
                    label: '取消',
                    className: 'btn-danger cancel_issue'
                }
            }
        });

    }

    function handleChange(order_id){
        bootbox.confirm("该操作不可逆，您确定要作废当前订单吗？", function(result){
            if(result){
                var parms={
                    pay_id:order_id,
                    '_csrf-backend':'<?=Yii::$app->request->csrfToken?>'
                };
                App.blockUI();
                $.post('<?=Yii::$app->urlManager->createUrl('finance/cancel')?>',parms,function(data){
                    App.unblockUI();
                    if(data.code == 'yes'){
                        showToastr('success',data.message);
                        setTimeout(function(){
                            window.location.reload();
                        },1500);
                    }else{
                        showToastr('error',data.message);
                    }
                });
            }
        })
    }
</script>
