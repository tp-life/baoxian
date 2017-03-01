
<div class="row">
    <div class="col-md-12">
<!--        <div class="note note-danger">-->
<!--            <p> 维修报价 </p>-->
<!--        </div>-->
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">维修报价</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">
                        <a class="btn blue btn-sm " href="javascript:void(0)" onclick="jionAll()">
                            <i class="fa fa-plus-square"></i>批量加入
                        </a>
                    </div>
                    &nbsp;
                    <div class="btn-group ">
                        <a class="btn red btn-sm " href="<?=Yii::$app->urlManager->createUrl('offer/index')?>">
                             返回报价
                        </a>
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
                            <th width="2%">
                                <input type="checkbox" class="group-checkable"></th>
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="15%"> 手机名称</th>
                            <th width="5%"> 品牌</th>
                            <th width="10%"> 型号</th>
                           <!-- <th width="8%"> 颜色</th>
                            <th width="10%"> 内屏报价</th>
                            <th width="10%"> 外屏报价</th>-->
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

    function handleStatus(id){
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['offer/create']);?>',{'_csrf-maintainer':$('meta[name="csrf-token"]').attr("content"),'id':id},function(data){
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

    function jionAll(){
        var ids =[];
        $('.offer_checkbox:checked:enabled').each(function(index,ele){
            ids.push($(this).val());
        });
        if(!ids.length){
            showToastr('error','请选择您所要加入的型号!');
            return false;
        }
        ids=ids.join(',');
        handleStatus(ids);
    }

</script>
