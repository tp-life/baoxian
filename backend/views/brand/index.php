<?php
$this->title = '数据表格';
?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?= \yii\helpers\Url::to(['brand/index']) ?>">品牌管理</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>品牌型号</span>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 品牌型号管理 </p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-red"></i>
                    <span class="caption-subject font-red sbold uppercase">品牌型号管理</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">

                           <a class="btn  blue  btn-sm " href="<?=Yii::$app->urlManager->createUrl('brand/create')?>">
                            <i class="fa fa-plus-square"></i> 新增型号</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            &nbsp;&nbsp;&nbsp;&nbsp;
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
                            <!--                            <th width="2%">-->
                            <!--                                <input type="checkbox" class="group-checkable"></th>-->
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 名称</th>
                            <th width="9%"> 所属上级</th>
                            <th width="7%"> 首字母</th>
                            <th width="8%"> 型号排序</th>
                            <th width="15%"> 操作</th>
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

    function deleteBrand(id){
        bootbox.confirm("删除当前型号,将同步删除该型号下所有的下级型号,确认删除吗？#"+id, function(result) {
            if(result) {
                $.getJSON('<?=Yii::$app->urlManager->createUrl(['brand/delete'])?>',{id:id},function(backdata){
                    if(backdata.code=='yes')
                    {
                        showToastr('success',backdata.message);
                        window.location.reload();
                    }else{
                        showToastr('error',backdata.message?backdata.message:'操作失败');
                    }
                });
            }
        });
    }
</script>
