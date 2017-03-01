<?php
$this->title = '数据表格';

?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">商家</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>维修商家</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 维修商家
    <small>维修商家列表管理</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 维修商家列表 </p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-user font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">维修商家</span>
                </div>
                <div class="actions">
                    <div class="btn-group " >
                        <a href="/user/create" class="btn  blue  btn-circle btn-sm ">
                            <i class="fa fa-plus-square"></i>  新增商户</a>
                    </div>
<!--                    <div class="btn-group">-->
<!--                        <a class="btn red btn-outline btn-circle" href="javascript:;" data-toggle="dropdown">-->
<!--                            <i class="fa fa-share"></i>-->
<!--                            <span class="hidden-xs"> 工具 </span>-->
<!--                            <i class="fa fa-angle-down"></i>-->
<!--                        </a>-->
<!--                        <ul class="dropdown-menu pull-right">-->
<!--                            <li>-->
<!--                                <a href="javascript:;"> </a>-->
<!--                            </li>-->
<!--                            <li class="divider"></li>-->
<!--                            <li>-->
<!--                                <a href="javascript:;"> Print Invoices </a>-->
<!--                            </li>-->
<!--                        </ul>-->
<!--                    </div>-->
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
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" size="30" id="datatable_search" name="filter" class="input-md form-filter" placeholder="请输入商户名称/商户ID进行查询"/>
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>

                        <tr role="row" class="heading">
<!--                            <th width="2%">-->
<!--                                <input type="checkbox" class="group-checkable"></th>-->
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="8%"> 商户名称</th>
                            <th width="8%"> 登陆账号</th>
                            <th width="21%"> 商户地址</th>
                            <th width="8%"> 联系人</th>
                            <th width="8%"> 联系电话</th>
                            <th width="20%"> 收款信息</th>
                            <th width="7%"> 合作状态</th>
                            <th width="20%"> 操作</th>
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
<script  type="text/javascript">
    $(function(){

        EcommerceList.init('/mainter/getdata',{},false,'datatable_ajax');
    });

    function handleStatus(seller_id,status){
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['mainter/change']);?>',{'_csrf-backend':$('meta[name="csrf-token"]').attr("content"),'seller_id':seller_id,'status':status},function(data){
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
