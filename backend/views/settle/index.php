<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">财务结算</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>商家结算明细</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 财务结算
    <small>商家结算明细</small>
</h3>
<div class="row">
    <div class="col-md-12">
                <div class="note note-danger">
                    <p> 1. 商家维修结算明细. </p>
                    <p> 2. <span style="color: red">结算打款前请先确认已经打款至商家.</span></p>
                </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">商家结算明细</span>
                </div>
                <div class="actions">
                    <div class="btn-group " >
                        <a href="javascript:void(0)" class="btn  yellow  btn-circle btn-sm " onclick="handleAll()">
                            <i class="fa fa-bell"></i>  批量结算打款</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm"
                                id="datatable_filter" name="status">
                                <option value="">结算状态</option>
                                <option value="1">待结算</option>
                                <option value="0">结算中</option>
                                <option value="2">结算成功</option>
                                <option value="3">结算失败</option>
                            </select>

                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" size="30" id="datatable_search" name="name"
                                   class="form-control input-inline input-md form-filter" placeholder="请输入商家名称或者维保订单ID进行查询"/>
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-search"></i> 搜索
                            </button>
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
                            <th width="8%"> 可结算金额</th>
                            <th width="10%"> 可结算时间</th>
                            <th width="8%"> 结算状态</th>
                            <th width="12%">结算完成时间</th>
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
        $('.settle_checkbox:checked').each(function(index,ele){
            ids.push($(this).val());
        });
        if(!ids.length){
            showToastr('error','请选择您所要结算打款的记录!');
            return false;
        }
        ids=ids.join(',');
        handleSettle(ids);
    }

    function handleStatus(seller_id) {
        handleSettle(seller_id);
    }

    function handleSettle(id){
        bootbox.prompt({
            title:"请输入打款备注,并确认已打款至商家？<p style='color: red;font-size: 14px;margin:0;padding: 3px;'>Eg:通过支付宝账号XXXX打款5000.00,支付账单号:343454545665</p>",
            size:"large",
            callback:function(result) {
                if(result) {
                    App.blockUI();
                    $.post('<?=Yii::$app->urlManager->createUrl(['settle/change']);?>', {
                        '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
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
                }else if(result !==null){
                    showToastr('error','请输入打款备注');
                }
            }
        } );
    }
</script>
