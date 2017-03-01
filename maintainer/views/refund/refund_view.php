<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?= \yii\helpers\Url::to(['cardrefund/index']) ?>">退回明细</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>退回明细#ID-<?= $_REQUEST['id'] ?></span>
        </li>
    </ul>
</div>
<!-- Begin: life time stats -->
<div class="portlet light portlet-fit portlet-datatable bordered">

    <div class="portlet-body">

        <div class="tab-content">
            <div class="tab-pane active">

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="portlet default  box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>退回详细
                                </div>
                                <div class="actions">
                                </div>
                            </div>
                            <div class="portlet-body ">
                                <div class="row">
                                    <div class="col-xs-10">
                                        <table  class="table table-bordered table-hover">
                                            <thead>

                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th>基本信息</th>
                                            </tr>
                                            <tr>
                                            <?php $wait_send = \common\models\CardRefund::getWaitSendPrice($model->pay_sn);
                                                $seller_info =Yii::$app->user->identity->getSellerInfo();
                                            ?>
                                                <th class="font-grey-salsa">退回编号</th>
                                                <td class="sbold"><?= $model->formatId; ?></td>

                                                <th class="font-grey-salsa">退回状态</th>
                                                <td  class="font-purple-seance sbold"><?= $model->statusText; ?></td>

                                            </tr>
                                            <tr>
                                                <th class="font-grey-salsa">退回险种</th>
                                                <td class="sbold"><?= $model->coverage_code; ?></td>

                                                <th class="font-grey-salsa">退回日期</th>
                                                <td><?= $model->add_time ? date('Y-m-d H:i:s', $model->add_time) : '' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="font-grey-salsa">退卡批次号</th>
                                                <td class="font-red-thunderbird"><?= $model->pay_sn; ?></td>
                                                <?php if(!$seller_info->pid): ?>
                                                <th class="font-grey-salsa">本批次申请卡券总金额</th>
                                                <td class="font-red-thunderbird"><?= $pay_info->total_price; ?></td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php if(!$seller_info->pid): ?>
                                            <tr>

                                                <th class="font-grey-salsa">本批次已发放卡券总价值</th>
                                                <td class="font-red-thunderbird"><?= $pay_info->send_total_price; ?></td>
                                                <th class="font-grey-salsa">本批次待发放卡券总价值</th>
                                                <td class="font-red-thunderbird"><?= $wait_send; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="font-grey-salsa">本批次已收金额</th>
                                                <td class="font-red-thunderbird"><?= $pay_info->received_price; ?></td>

                                                <th class="font-grey-salsa">本批次应退卡总金额</th>
                                                <td class="font-red-thunderbird"><?= $pay_info->back_price; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="font-grey-salsa">本批次实际退卡总金额</th>
                                                <td class="font-red-thunderbird"><?= $pay_info->real_back_price; ?></td>
                                                <th class="font-grey-salsa">本次退卡总额</th>
                                                <td class="font-red-thunderbird"><?= $model->total_price; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <th class="font-grey-salsa">退回数量</th>
                                                <td class=""><?= $model->number; ?></td>

                                                <th class="font-grey-salsa">申领退回商家</th>
                                                <td>
                                                    <?php $seller = $model->getFromSellerInfo(); ?>
                                                    <?php if($seller){
                                                        echo $seller->seller_name.'['.$seller->concat_tel.']';
                                                    } ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>退回卡券信息</th>
                                                <th><?= \common\widgets\Rexport::widget(['id'=>$model->id]) ?></th>
                                            </tr>
                                            <tr>
                                                <th class="font-grey-salsa">卡券编号</th>
                                                <th class="font-grey-salsa">险种编码</th>
                                                <th class="font-grey-salsa">保险名称</th>
                                                <th class="font-grey-salsa">所属保险类型</th>
                                                <th class="font-grey-salsa">所属保险公司</th>
                                            </tr>
                                            <?php if($card = $model->getCardInfo()): ?>
                                                <?php foreach($card as $item): ?>
                                                    <tr>
                                                        <td class=""><?= $item->card_number; ?></td>
                                                        <td class=""><?= $item->coverage_code?></td>
                                                        <?php if($c_info = $item->getCoverageInfo()): ?>
                                                            <td><?= $c_info->coverage_name?></td>
                                                            <td><?= $c_info->type_name?></td>
                                                            <td><?= $c_info->company_name?></td>
                                                        <?php else: ?>
                                                            <td  class="font-red-thunderbird sbold">卡券关联异常</td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <tr>
                                                <th>问题卡券信息</th>
                                            </tr>
                                            <tr>
                                                <th class="font-grey-salsa">卡券编号</th>
                                                <th class="font-grey-salsa">险种编码</th>
                                                <th class="font-grey-salsa">保险名称</th>
                                                <th class="font-grey-salsa">所属保险类型</th>
                                                <th class="font-grey-salsa">所属保险公司</th>
                                            </tr>
                                            <?php if($card = $model->getErrCardInfo()): ?>
                                                <?php foreach($card as $item): ?>
                                                    <tr>
                                                        <td class=""><?= $item->card_number; ?></td>
                                                        <td class=""><?= $item->coverage_code?></td>
                                                        <?php if($c_info = $item->getCoverageInfo()): ?>
                                                            <td><?= $c_info->coverage_name?></td>
                                                            <td><?= $c_info->type_name?></td>
                                                            <td><?= $c_info->company_name?></td>
                                                        <?php else: ?>
                                                            <td  class="font-red-thunderbird sbold">卡券关联异常</td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>


                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="portlet  default  box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>退回操作日志
                                </div>
                                <div class="actions">
                                </div>
                            </div>

                            <div class="portlet-body ">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class=" table table-bordered  table-hover">
                                            <thead>
                                            <tr>
                                                <th width="15%">操作时间</th>
                                                <th width="20%">操作人员</th>
                                                <th>操作内容</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if($log = $model->getLogInfo()): ?>
                                                <?php foreach($log as $k=>$v): ?>
                                                    <tr <?php if($k==0): ?>style="color: red"<?php endif; ?> >
                                                        <td><?= $v['update_time'] ?></td>
                                                        <td><?= $v['name'] ?></td>
                                                        <td><?= $v['content'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- End: life time stats -->

<script type="text/javascript">
    $(function(){

        $('#rf_confirm_done').bind('click',function(){
//            bootbox.prompt({
//                size: "large",
//                title: "请填写确认退卡备注?",
//                callback: function(result){
//                    if(result){
//                        refundCardConfirm(result);
//                    }
//                }
//            })
            var html ='<form id="real_refund" class="form-horizontal" method="post">'+
                '<input type="hidden" name="refund_id" value="<?=$model->id?>"><input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">'+
                '<div class="form-body">'+
                '<p class="font-red-thunderbird sbold">'+$('#refund_warinning').html()+'</p>'+
                '<div class="form-group   margin-top-20">'+
                '<label class="control-label col-md-3">实际退卡金额</label>'+
                '<div class="col-md-8">'+
                '<input type="text" class="form-control" name="real_price" placeholder="不需退款可以不填" > </div>'+
                '</div>'+
                '<div class="form-group">'+
                '<label class="control-label col-md-3">退卡备注</label>'+
                '<div class="col-md-8">'+
                '<textarea class="form-control" rows="2" name="refund_content"></textarea>'+
                '</div>';
            bootbox.dialog({
                title:'退卡确认',
                message:html,
                buttons: {
                    confirm: {
                        label: '确认退卡',
                        className: 'btn-success',
                        callback:function(result){
                            var data = $('#real_refund').serialize();
                            refundCardConfirm(data);
                            return false;
                        }
                    },
                    cancel: {
                        label: '取消',
                        className: 'btn-danger cancel_issue'
                    }
                },
            })
        });
    })

    function refundCardConfirm(data)
    {
		App.startPageLoading();
        $.post(
            '<?= \yii\helpers\Url::to(['finance/cardrefund']) ?>',
            data,
            function(e){
                //console.log(e);
                if(e.code == 'yes'){
                    showToastr('success', e.message,'','toast-top-right');
                    setTimeout("window.location.reload()",2000);
                    ;
                }else{
                    bootbox.alert(e.message,function(){});
                    //showToastr('error', e.message,'','toast-top-right');
                }
            },
            'json'

        );
        App.stopPageLoading();
    }
</script>
