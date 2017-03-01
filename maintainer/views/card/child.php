
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">我的卡券</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">
                        <a class="btn     btn-sm " onclick="downloadCard()">
                            <i class="fa fa-download"></i> 导出卡券
                        </a>
                    </div>
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

                                </div>
                                <div class="col-sm-9">
                                    <div class="table-search text-right">
                                        <select
                                            class="table-group-action-input form-control form-filter input-inline  input-sm"
                                            id="datatable_filter" name="status">
                                            <option value="">卡券状态</option>
                                            <option value="0">未激活</option>
                                            <option value="1">已激活</option>
                                            <option value="2">已失效</option>
                                            <option value="3">冻结中</option>
                                        </select>
                                        <input type="hidden" class="form-filter" name="type" value="2">
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入 险种 进行查询"/>
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
                            <th width="10%"> 卡券号</th>
                            <th width="8%">  险种</th>
                            <th width="8%">  领用ID号</th>
                            <th width="10%"> 卡券状态</th>
                            <th width="18%"> 卡券流转</th>
                            <th width="10%"> 时间</th>
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
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('card/getchild')?>', {}, false, 'datatable_ajax');
    });
    function downloadCard() {
        var url ='<?=Yii::$app->urlManager->createUrl('card/cdownload')?>';
        var data = $('#datatable_form').serialize();
        window.location.href =url+'?'+data;
    }

</script>
