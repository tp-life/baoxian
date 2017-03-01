<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">品牌管理</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>报价商家</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 品牌管理
    <small>报价商家</small>
</h3>
<div class="row">
    <div class="col-md-12">

        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">报价商家</span>
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
                                id="datatable_filter" name="type">
                                <option value="">商家名称</option>
                                <option value="1">手机名称</option>
                            </select>
                            <input type="text" size="30" id="datatable_search" name="name" class="form-control input-inline input-md form-filter" placeholder="请输入关键字进行查询"/>
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
                            <th width="10%"> 商家</th>
                            <th width="15%"> 手机名称</th>
                            <th width="5%"> 品牌</th>
                            <th width="10%"> 型号</th>
                            <th width="8%"> 颜色</th>
                            <th width="10%"> 内屏报价</th>
                            <th width="10%"> 外屏报价</th>
                            <th width="8%"> 平台服务费</th>
                            <th width="8%">状态</th>
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
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl(['offermain/index','offer_id'=>$offer_id])?>', {}, false, 'datatable_ajax');
    });

    function handleStatus(seller_id,status){
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['offer/change']);?>',{'_csrf-backend':$('meta[name="csrf-token"]').attr("content"),'offer_id':seller_id,'status':status},function(data){
            App.unblockUI();
            if(data.code !='yes'){
                showToastr('error',data.message);
                return false;
            }
            showToastr('success',data.message);
            setTimeout(function(){
                window.location.reload();
            },1000);
        });
    }
</script>
