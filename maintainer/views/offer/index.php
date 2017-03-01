
<div class="row">
    <div class="col-md-12">
<!--        <div class="note note-danger">-->
<!--            <p> 维修报价 </p>-->
<!--        </div>-->
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings  font-green"></i>
                    <span class="caption-subject  font-green sbold uppercase">维修报价</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">

                        <a class="btn blue  btn-sm" href="<?=Yii::$app->urlManager->createUrl('offer/create')?>">
                            <i class="fa fa-plus-square"></i> 新增报价</a>
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
                           <!-- <th width="8%"> 颜色</th>-->
                           <!-- <th width="10%"> 内屏报价</th>-->
                          <!--  <th width="10%"> 外屏报价</th>-->
<!--                            <th width="8%"> 平台服务费</th>-->
                            <th width="8%">状态</th>
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
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('offer/getdata')?>', {}, false, 'datatable_ajax');
    });

    function handleStatus(id,status){
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['offer/change']);?>',{'_csrf-maintainer':$('meta[name="csrf-token"]').attr("content"),'id':id,'status':status},function(data){
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

    function handleDelete(id){
        bootbox.confirm("确认要删除当前报价吗？#"+id, function(result) {
            App.blockUI();
            $.post('<?=Yii::$app->urlManager->createUrl(['offer/deleted']);?>', {
                '_csrf-maintainer': $('meta[name="csrf-token"]').attr("content"),
                'id': id
            }, function (data) {
                App.unblockUI();
                if (data.code != 'yes') {
                    showToastr('error', data.message);
                    return false;
                }
                showToastr('success', data.message);
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            });
        })
    }
</script>

<script type="text/javascript">
	$(function(){
		$.get('<?= \yii\helpers\Url::to(['offer/showlog']) ?>',function(text){
			//console.log(text);
			if(text.length!=0){
				bootbox.alert({
					size: "large",
					title: "报价变动提醒,<span class=\"font-green-sharp\">以下报价变动系统为避免利益损失系统自动处理为暂停，须重启才能正常服务客户</span>",
					message: text,
					callback: function(){
					}
				})
			}
		})
	})
</script>

