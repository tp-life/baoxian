
<div class="row">
    <div class="col-sm-12">
		<table  class="table table-bordered table-hover">
			<thead>

			</thead>
			<tbody>

			<tr>
				<th class="font-purple-medium">摘要信息</th>
			</tr>
			<tr>
				<th class="font-grey-salsa">商家</th>
				<td><?=Yii::$app->user->identity->getSellerInfo()->seller_name?></td>
			</tr>
			<tr>
				<th class="font-grey-salsa">统计</th>
				<td>当前可用于提现订单: <span class="font-purple-medium"><?=$count?></span> 笔,维修总费用: <span class="font-purple-medium"><?=$total?></span> ,
					可结算金额共: <span class="font-purple-medium"><?=$sum?></span> 元</td>
			</tr>
			<tr>
				<th class="font-grey-salsa">操作</th>
				<td><a href="javascript:void(0)" title="选中并执行提现操作" class="btn green" onclick="handleStatus('-1',<?=$count?>,'<?=$sum?>')">
						全部提现</a></td>
			</tr>
			</tbody>
			</table>

    </div>
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">商家结算明细</span>
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
                                <div class="col-sm-3 text-left">
                                    <div class="btn-group " >
                                        <a href="javascript:void(0)" class="btn red-sunglo" onclick="handleAll()">
                                            <i class="fa fa-bell"></i>  批量提现</a>
                                    </div>
                                </div>
                                <div class="col-sm-9 text-right">
                                    <select
                                        class="table-group-action-input form-control form-filter input-inline  input-sm"
                                        id="datatable_filter" name="status">
                                        <option value="">提现状态</option>
                                        <option value="1">待提现</option>
                                        <option value="0">提现中</option>
                                        <option value="2">提现成功</option>
                                        <option value="3">提现失败</option>
                                    </select>

                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="text" size="30" id="datatable_search" name="name"
                                           class="form-control input-inline input-md form-filter" placeholder="请输入维保订单ID进行查询"/>
                                    <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                        <i class="fa fa-search"></i> 搜索
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="2%">
                                <input type="checkbox" class="group-checkable">
                            </th>
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 商家名称</th>
                            <th width="8%"> 维保订单ID</th>
                            <th width="8%"> 结算金额</th>
                            <th width="10%"> 平台服务费</th>
                            <th width="8%"> 可提现金额</th>
                            <th width="10%"> 可提现时间</th>
                            <th width="8%"> 提现状态</th>
                            <th width="12%">提现完成时间</th>
                            <th width="18%"> 操作</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('settle/getdata')?>', {}, false, 'datatable_ajax');
    });

    function handleAll(){
        var ids =[];
        var price=0;
        $('.settle_checkbox:checked').each(function(index,ele){
            ids.push($(this).val());
            price +=parseFloat($(this).attr('data-price'));
        });
        if(!ids.length){
            showToastr('error','请选择您所要申请提现的记录!');
            return false;
        }
        var num =ids.length;
        ids=ids.join(',');
        handleSettle(ids,num,price);
    }

    function handleStatus(id,num,price) {
        handleSettle(id,num,price);
    }

    function handleSettle(id,num,price){
        if(num < 1){
            showToastr('error','当前没有可提现的订单!');
            return false;
        }
        bootbox.confirm('您有 <span class="font-red-intense">'+num +'</span> 笔订单的提现申请,共计: <span class="font-red-intense">'+price+' </span> 元人民币..',function(result) {
            if(result) {
                App.blockUI();
                $.post('<?=Yii::$app->urlManager->createUrl(['settle/change']);?>', {
                    '_csrf-maintainer': $('meta[name="csrf-token"]').attr("content"),
                    'id': id,
                    remark:result
                }, function (data) {
                    App.unblockUI();
                    if (data.code != 'yes') {
                        showToastr('error', data.message);
                        return false;
                    }
                    showToastr('success', data.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                });
            }
        } );
    }
</script>
