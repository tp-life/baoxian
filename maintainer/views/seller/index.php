
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-user font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">下级商家列表</span>
                </div>
                <div class="actions">
                    <div class="btn-group " >
                        <a href="<?=Yii::$app->urlManager->createUrl('seller/createuser')?>" class="btn blue-hoki">
                            <i class="fa fa-plus-square"></i>  新增下级商家</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                    <span> </span>


                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm"
                                id="datatable_filter" name="status">
                                <option value="">合作状态</option>
                                <option value="2">合作中</option>
                                <option value="1">已终止</option>
                            </select>
                            &nbsp;&nbsp;
                            <input type="text" size="30" id="datatable_search" name="filter" class="input-md form-filter form-control input-inline" placeholder="请输入商户名称/商户ID进行查询"/>
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>

                        <tr role="row" class="heading">
                            <th width="8%"> 商户名称</th>
                            <th width="8%"> 登陆账号</th>
                            <th width="8%"> 联系人</th>
                            <th width="8%"> 联系电话</th>
                            <th width="25%"> 收款信息</th>
                            <th width="7%"> 合作状态</th>
                            <th width="12%">账户类型</th>
                            <th width="20%"> 操作</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script  type="text/javascript">
    $(function(){

        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('seller/getdata')?>',{},false,'datatable_ajax');
    });

    function handleStatus(seller_id,status){
        var data={'_csrf-maintainer':$('meta[name="csrf-token"]').attr("content"),'seller_id':seller_id,'status':status};
        submitFrom(data,'<?=Yii::$app->urlManager->createUrl(['seller/change']);?>','<?=Yii::$app->urlManager->createUrl(['seller/index']);?>',1000);
    }
</script>
