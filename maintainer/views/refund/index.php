
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">卡券退卡列表</span>
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
                                <div class="col-sm-3 text-left">
                                    <div class="btn-group ">

                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="table-search text-right">
                                        <select
                                            class="table-group-action-input form-control form-filter input-inline  input-sm"
                                            id="datatable_filter" name="status">
                                            <option value="">退卡状态</option>
                                            <option value="0">退卡中</option>
                                            <option value="2">退卡成功</option>
                                            <option value="3">退卡失败</option>
                                        </select>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入卡券号进行查询"/>
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
                            <th width="8%"> 退卡批次号</th>
                            <th width="10%">  退卡险种</th>
                            <th width="8%">  退卡数量</th>
                            <?php if(!$seller_info['pid']): ?><th width="8%">  退卡总金额</th><?php endif; ?>
                            <th width="8%">  退卡状态</th>
                            <th width="10%">  申请时间</th>
                            <th width="8%"> 操作</th>
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
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('refund/index')?>', {}, false, 'datatable_ajax');
    });

    function showInfo(id){
        $.get('<?=Yii::$app->urlManager->createUrl('refund/info')?>',{id:id},function(data){
            if(data.code =='yes'){
                var result = data.data.info;
                var log =data.data.log;
                var pid =data.data.pid;
                var html='<table class="table table-bordered table-hover">';
                html+='<thead>';
                var p =pid > 0?'':'<th>退卡金额</th><td colspan="7" class="font-purple-seance"> ¥'+result.total_price+'</td>';
                html +='<tr><th>退款险种</th><td colspan="7">'+result.coverage_code+'</td></tr>';
                html +='<tr><th>退卡数量</th><td colspan="7" class="font-purple-seance">'+result.number+'</td></tr><tr>'+p+'</tr>';
                html +='<tr><th>退款时间</th><td colspan="7">'+result.add_time+'</td></tr>';
                html +='<tr><th >退卡卡号</th><td colspan="7">'+result.card_numbers+'</td></tr><tr><td colspan="7">&nbsp;&nbsp;</td></tr>'
                html +='<tr><th colspan="7" style="height: 30px;line-height: 30px;">退卡日志记录</th></tr><tr><th>时间</th><th>操作人员</th><th>内容</th></tr></thead><tbody>';
                for (var i = 0; i< log.length;i++){
                    var color ='';
                    if(i == 0){
                        color ='font-purple-seance';
                    }
                    html +='<tr class="'+color+'">';
                    html +='<td>'+log[i].update_time+'</td><td>'+log[i].name+'</td><td>'+log[i].content+'</td>';
                    html +='</tr>';
                }
                html+='</tbody></table>';
                bootbox.dialog({
                    message:html,
                    size:'large',
                    title:'退卡详情#'+id
                });

            }
        });
    }


    function cancel(id){
        bootbox.confirm("该操作不可逆，您确定要取消该退卡吗？", function(result){
            if(result){
                var parms={
                    id:id,
                    '_csrf-maintainer':'<?=Yii::$app->request->csrfToken?>'
                };
                App.blockUI();
                $.post('<?=Yii::$app->urlManager->createUrl('refund/cancel')?>',parms,function(data){
                    App.unblockUI();
                    if(data.code == 'yes'){
                        showToastr('success',data.message);
                        setTimeout(function(){
                            window.location.reload();
                        },1500);
                    }else{
                        showToastr('error',data.message);
                    }
                });
            }
        })
    }

</script>
