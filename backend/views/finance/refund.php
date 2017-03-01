
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">商家退卡列表</span>
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
                                            <option value="">退卡状态</option>
                                            <option value="1">待退卡</option>
                                            <option value="2">退卡成功</option>
                                            <option value="3">退卡失败</option>
                                        </select>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <select
                                            class="table-group-action-input form-control form-filter input-inline  input-sm"
                                            name="type">
                                            <option value="">商家名称</option>
                                            <option value="1">批次号</option>
                                        </select>
                                        <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入商家名称或批次号进行查询"/>
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
                            <th width="8%">  卡券批次号</th>
                            <th width="9%">  本批次已发放卡券价值</th>
                            <th width="8%">  本批次已收款</th>
                            <th width="7%">  退卡险种</th>
                            <th width="7%">  可退卡数量</th>
                            <th width="8%">  本次可退卡金额</th>
                            <th width="7%">  退卡状态</th>
                            <th width="10%">  申请时间</th>
                            <th width="10%"> 操作</th>
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
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('finance/refund')?>', {}, false, 'datatable_ajax');
    });
</script>
