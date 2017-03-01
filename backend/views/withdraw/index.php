<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">财务结算</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>商家提现记录</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 财务结算
    <small>商家提现记录</small>
</h3>
<div class="row">
    <div class="col-md-12">
                <div class="note note-danger">
                    <p><span style="color: red">请输入商家名称或者维保订单ID进行查询</span></p>
                </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">商家提现记录</span>
                </div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">

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
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="15%"> 商家名称</th>
                            <th width="8%"> 维保订单ID</th>
                            <th width="8%"> 结算金额</th>
                            <th width="30%" class="text-center"> 备注</th>
                            <th width="8%"> 结算操作人</th>
                            <th width="12%">结算完成时间</th>
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
