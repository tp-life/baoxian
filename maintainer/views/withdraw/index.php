
<div class="row">
    <div class="col-md-12">

        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">商家提现记录</span>
                </div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">

                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="15%"> 商家名称</th>
                            <th width="8%"> 维保订单ID</th>
                            <th width="8%"> 提现金额</th>
                            <th width="30%" class="text-center"> 备注</th>
                            <th width="8%"> 提现操作人</th>
                            <th width="12%">提现完成时间</th>
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
        EcommerceList.init('<?=$url?>', {}, false, 'datatable_ajax');
    });

</script>
