<?php
$this->title = '数据表格';
?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">品牌管理</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>维修报价</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 品牌管理
    <small>维修报价</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 维修报价 </p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">维修报价</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">

                        <a class="btn  blue   btn-sm " href="<?=Yii::$app->urlManager->createUrl('offer/create')?>">
                            <i class="fa fa-plus-square"></i> 新增维修报价
                        </a>
                        &nbsp;
                        <a class="btn     btn-sm " onclick="downloadOffer()">
                            <i class="fa fa-download"></i> 导出报价
                        </a>
                        <a class="btn  green   btn-sm " id="upload_offer">
                            <i class="fa fa-upload"></i> 导入报价
                        </a>
                        <input type="file" name="offer" style="display: none" id="upload_offer_input">
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
                            <th width="15%"> 手机名称</th>
                            <th width="5%"> 品牌</th>
                            <th width="10%"> 型号</th>
                            <th width="8%"> 颜色</th>
                            <th width="10%"> 内屏报价</th>
                            <th width="10%"> 外屏报价</th>
                            <th width="8%"> 平台服务费</th>
                            <th width="8%">状态</th>
                            <th width="8%">维修商家数</th>
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
<script src="<?= Yii::getAlias('@js'); ?>/ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl(['offer/getdata'])?>', {}, false, 'datatable_ajax');

        $('#upload_offer').on('click',function(){
            $('#upload_offer_input').click();
        });
        $('#upload_offer_input').on('change',function () {
            App.blockUI();
            $.ajaxFileUpload({
                url: '<?= Yii::$app->urlManager->createUrl('offer/import')?>',
                secureuri: false,
                fileElementId: 'upload_offer_input',
                data: {
                    '_csrf-backend':'<?= Yii::$app->request->csrfToken ?>'
                },
                dataType : 'json',
                success: function (data) {
                    App.unblockUI();
                    if(data.status == 1){
                        showToastr('success',data.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1500)
                    }else{
                        showToastr('error',data.msg);
                    }
                },
                error: function (data, status, e) {
                    App.unblockUI();
                    showToastr('error', e);
                },
            })
        })

    });

    function downloadOffer() {
        var url ='<?=Yii::$app->urlManager->createUrl('offer/download')?>';
        var data = $('#datatable_form').serialize();
        window.location.href =url+'?'+data;
    }
    
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
