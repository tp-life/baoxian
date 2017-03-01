<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?= \yii\helpers\Url::to(['order/index']) ?>">订单</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>保险订单详细#ID-<?= $_REQUEST['id'] ?></span>
        </li>
    </ul>

</div>

<style>
    .portlet-body h4{ padding-bottom: 5px; margin-top: 25px;color: #222!important;font-weight: bolder;font-size: 20px;}
    .portlet-body hr{ margin-top: 5px}
</style>
<div class="row">
    <?php if ($order['order_state'] == 23): ?>
        <div class="col-md-12">
            <div class="note note-danger">
                <span style="color: red">对不起，客户资料填写有误，请核对后重新填写哦！如有疑问请联系客服：400-0900-299</span>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($order['order_state'] == 22): ?>
        <div class="col-md-12">
            <div class="note note-danger">
                <span style="color: red">备注：审核成功保单返回一般情况保险第8天0点生效！</span>
            </div>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <!-- Begin: life time stats -->
            <div class="portlet default box">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-green"></i>
                        <span class="caption-subject font-green"><?= $order['coverage_name'] ?></span>
                    </div>
                    <div class="actions" style="padding: 0px;">
                        <?php if ($order['order_state'] == 21) { ?>
                            <button class="btn green btn-lg" type="button" onclick="audit_orders(1)"
                                    style="height: 40px;">
                                <i class="fa fa-save"></i>
                                审核通过
                            </button>
                            <button class="btn red btn-lg" type="button" onclick="audit_orders(2)" style="height: 40px;">
                                <i class="fa fa-times"></i>
                                审核失败
                            </button>
                        <?php } else if ($order['order_state'] == 22) { ?>
                            <button class="btn red btn-lg" type="button" onclick="updaetCoverage()"
                                    style="height: 40px;">
                                <i class="fa fa-bookmark"></i>
                                手动更新保单号
                            </button>
                        <?php }; ?>
                    </div>
                </div>
                <div class="portlet-body">

                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
									<table  class="table table-bordered table-hover">
										<thead>

										</thead>
										<tbody>
										<tr>
											<th>基础信息</th>
										</tr>
										<tr>

											<th class="font-grey-salsa">订单号</th>
											<td><?= $order['order_sn'] ?></td>
											<th class="font-grey-salsa">商家名称</th>
											<td><?= $order['seller_name'] ?></td>

										</tr>
										<tr>
											<th class="font-grey-salsa">IMEI串号</th>
											<td>  <?= $order['imei_code'] ?></td>
											<th class="font-grey-salsa">品牌型号</th>
											<td><?= $brand ?></td>
										</tr>
										<tr>

											<th class="font-grey-salsa">投保客户</th>
											<td><?= $order['member_name'] ?></td>
											<th class="font-grey-salsa">联系电话</th>
											<td><?= $order['member_phone'] ?></td>

										</tr>
										<tr>
											<th class="font-grey-salsa">订单状态</th>
											<td class="font-purple-seance">  <?= $status ?></td>
											<th class="font-grey-salsa">支付方式</th>
											<td><?= \common\library\helper::orderPaymentName($order['payment_code']) ?></td>
										</tr>
										<tr>
											<th class="font-grey-salsa">投保金额</th>
											<td> <?= $order['order_amount'] ?></td>
											<th class="font-grey-salsa">添加时间</th>
											<td><?= date('Y-m-d H:i:s', $order['add_time']) ?></td>
										</tr>
                                        <?php if($order['payment_code'] =='kaquan' && ($cardInfo = \common\models\CardCouponsGrant::getInfoByOrder($order['order_id']))): ?>
                                            <tr>
                                                <th>卡券信息</th>
                                            </tr>
                                            <tr>

                                                <th class="font-grey-salsa">卡券编号(秘钥)</th>
                                                <td><?= $cardInfo['card_number'].'（'.$cardInfo['card_secret'].'）' ?></td>
                                                <th class="font-grey-salsa">商家名称</th>
                                                <td> <?= $cardInfo['seller_name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>商家联系方式</td>
                                                <th><?=$cardInfo['concat_tel']?></th>
                                            </tr>
                                        <?php endif; ?>

										<tr>
											<th>机主信息</th>
										</tr>
										<tr>

											<th class="font-grey-salsa">机主姓名</th>
											<td><?= $order['buyer'] ?></td>
											<th class="font-grey-salsa">机主手机</th>
											<td> <?= $order['buyer_phone'] ?></td>

										</tr>
										<tr>
											<th class="font-grey-salsa">机主身份证</th>
											<td> <?= $order['idcrad'] ?></td>
											<th class="font-grey-salsa"></th>
											<td> </td>
										</tr>
										<tr>
											<th>保障信息</th>
										</tr>
										<tr>

											<th class="font-grey-salsa">保险公司</th>
											<td><?= $coverage->company_name ?></td>
											<th class="font-grey-salsa">保险类型</th>
											<td> <?= $coverage->type_name ?></td>

										</tr>
										<tr>

											<th class="font-grey-salsa">保险名称</th>
											<td><?= $coverage->coverage_name ?></td>
											<th class="font-grey-salsa">保险代码</th>
											<td> <?= $coverage->coverage_code ?> </td>

										</tr>
										<tr>

											<th class="font-grey-salsa">保险期限</th>
											<td>
												<?= $order['start_time']?date('Y-m-d', $order['start_time']):'--' ?>&nbsp;&nbsp;
                                                至 &nbsp;&nbsp;
												 <?= $order['end_time']?date('Y-m-d', $order['end_time']):'--' ?></td>
											<th class="font-grey-salsa">保单号</th>
											<td style="color: red"> <?= $order['policy_number'] ? $order['policy_number'] : '保单号缺失' ?> </td>

										</tr>


										<tr>
											<th>上传资料</th>
										</tr>
										<tr>
											<th class="font-grey-salsa">

											</th>
											<td>
												<form method="post" enctype="multipart/form-data">
													<input type="file" name="img" style="display: none;"
														   id="order_view_input">
												</form>
												<div class="col-xs-5 text-center">
													<div class="mt-element-ribbon bg-grey-steel">
														<div
															class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase">
															<div class="ribbon-sub ribbon-clip"></div>
															手机正面
														</div>
														<p>

															<img width="170" height="130" class="show_img" id="imei_face_image"
																 src="<?= $order['imei_face_image'] ? $order['imei_face_image'] : Yii::getAlias('@image') . '/default.png' ?>"/>

														</p>
													</div>

												</div>
											</td>
										</tr>
										<tr>
											<th class="font-grey-salsa">

											</th>
											<td>
												<div class="col-xs-5 text-center">
													<div class="mt-element-ribbon bg-grey-steel">
														<div
															class="ribbon ribbon-border-hor ribbon-clip ribbon-color-success uppercase">
															<div class="ribbon-sub ribbon-clip"></div>
															手机背面
														</div>
														<p>
															<img width="170" height="130" class="show_img" id="imei_back_image"
																 src="<?= $order['imei_back_image'] ? $order['imei_back_image'] : Yii::getAlias('@image') . '/default.png' ?>"/>
														</p>
													</div>
												</div>

											</td>
										</tr>
										</tbody>
										</table>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="portlet default  box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>客服备注

                                            </div>

                                        </div>
                                        <div class="portlet-body ">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <textarea class="form-control" id="server_mark"
                                                              rows="3"><?= $order['server_mark'] ?></textarea>
                                                </div>
                                            </div>
											<?php if (!in_array($order['order_state'], ['0', '40', '50', '70', '80']) && $order['end_time'] > time()){ ?>
												<div class="actions">
													<a class="btn green btn-default col-md-offset-0"
													   style="margin-top:5px;" id="server_mark_button"
													   href="javascript:;">
														<i class="fa fa-save"></i>
														保存
													</a>
												</div>
												<?php }else{ ?>
												<div class="actions">
													<a class="btn default col-md-offset-0" style="margin-top:5px;"
													   href="javascript:showToastr('error', '此状态不支持修改');">
														此状态不支持修改
													</a>
												</div>
											<?php } ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="portlet default  box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>操作日志
                                            </div>
                                            <div class="actions">
                                            </div>
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <table class=" table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th width="15%">操作人员</th>
                                                            <th width="60%">备注</th>
                                                            <th width="20%">操作时间</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($order_log as $key=> $val): ?>
                                                            <tr <?php if($key==0): ?> style="color: red" <?php endif; ?> >
                                                                <td><?= $val->log_user ?></td>
                                                                <td><?= $val->log_msg ?></td>
                                                                <td><?= $val->log_time ?></td>
                                                            </tr>
                                                        <?php endforeach ?>
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
												<i class="fa fa-cogs"></i>理赔明细
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
															<th width="20%">客户[电话]</th>
															<th width="15%">理赔状态</th>
															<th>理赔时间</th>
															<th width="20%">操作</th>

														</tr>
														</thead>
														<tbody>
														<?php if( $main_order): ?>
															<?php foreach($main_order as $k=>$v): ?>
																<tr <?php if($k==0): ?>style="color: red"<?php endif; ?> >
																	<td><?= $v['contact'].'['.$v['contact_number'].']' ?></td>
																	<td><?= $v->getStateText() ?></td>
																	<td><?= date('Y-m-d H:i',$v['add_time']) ?></td>
																	<td><a target="_blank" class="btn red btn-xs  btn-default" href="<?= \yii\helpers\Url::to(['ordermainten/view','id'=>$v['id']]) ?>"><i class="fa fa-share"></i> 查看明细</a></td>


																</tr>
															<?php endforeach; ?>
														<?php else: ?>
															<tr style="color: red" >
																<td colspan="4">无理赔记录</td>

															</tr>

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
        </div>
    </div>
</div>
<!-- END PAGE LEVEL PLUGINS -->
<!-- begin coverage modal -->
<div class="modal fade" id="update_coverage_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content " role="document">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">更新保单号</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="order_update_form">
                            <div class="form-body">
                                <div class="form-group  margin-top-20">
                                    <label class="control-label col-md-3">保单号
                                        <span class="required"> * </span>
                                    </label>

                                    <div class="col-md-9">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="policy_number"
                                                   placeholder="保单号码"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <div class="col-md-12">
                    <div style="height: 20px;"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="submit_coverage_order">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end coverage modal -->
<script src="<?= Yii::getAlias('@js'); ?>/ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        var obj, tag;
        $('.change_pic').on('click', function () {
            obj = this;
            tag = $(this).attr('data-tag');
            $('#order_view_input').click();

        });


        $('#order_view_input').on('change', function () {
            var src = getFullPath($(this)[0]);
            $(obj).parent('.text-center').find('img').attr('src', src);

            $.ajaxFileUpload({
                url: '/order/upload',
                secureuri: false,
                fileElementId: 'order_view_input',
                data: {
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                    'order_id':<?=$order['order_id']?>,
                    'tag': tag
                },
                dataType: 'json',
                success: function (data, status) {
                    if (typeof(data.status) != 'undefined') {
                        if (data.status != 1) {
                            showToastr('error', data.msg);
                        }
                    }
                },
                error: function (data, status, e) {
                    showToastr('error', e);
                }
            })

        });

        $('#server_mark_button').on('click', function () {
            var remark = $('#server_mark').val();
            var type;
            $.post('<?=Yii::$app->urlManager->createUrl("order/remark")?>', {
                '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                'order_id':<?=$order['order_id']?>,
                'text': remark
            }, function (data) {
                if (data.code == 'yes') {
                    type = 'success';
                } else {
                    type = 'error';
                }
                showToastr(type, data.message);
            });
        });

        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true,
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            startDate:'<?=date('Y-m-d',strtotime('-7 day'))?>'
        });

    });

    /**
     * 订单审核
     * @param status
     */
    function audit_order(status) {
        bootbox.prompt({
            title: "保险订单审核备注",
            inputType: 'textarea',
            callback: function (result) {
                if (result == null) {
                    return false;
                } else {
                    App.blockUI();
                    $.post('<?= \yii\helpers\Url::to(['order/changorder']) ?>', {
                        '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                        'order_id':<?=$order['order_id']?>,
                        'status': status,
                        'text': result
                    }, function (data) {
                        App.unblockUI();
                        if (data.code == 'yes') {
                            showToastr('success', data.message);
                            setTimeout(function () {
                                window.location.reload();
                            });
                        } else {
                            showToastr('error', data.message);
                        }
                    });
                }
            }
        });
    }

    function audit_orders(status) {
        var err='';
        if(status == 2){
            err ='<div class="form-group">'+
                '<label class="control-label col-md-2">失败原因：</label>'+
                '<div class="col-md-10">'+
                '<div class="radio-list col-sm-12"><label class="radio-inline"><input type="radio" name="err_status"  value="1"> 照片不符合要求</label>&nbsp;&nbsp;&nbsp;'+
                '<label class="radio-inline"><input type="radio"  name="err_status"    value="2"> IMEI号码错误</label>&nbsp;&nbsp;&nbsp;'+
                '<label class="radio-inline"><input type="radio" name="err_status"     value="3"> 品牌型号错误</label>&nbsp;&nbsp;&nbsp;'+
                '<label class="radio-inline"><input type="radio" name="err_status"  value="0" checked="checked"> 其他</label></div></div>'+
                '</div>';
        }
        var html ='<form id="order_audit" class="form-horizontal" method="post">'+
            '<input type="hidden" name="order_id" value="<?=$order['order_id']?>">'+
            '<input type="hidden" name="_csrf-backend" value="'+ $('meta[name="csrf-token"]').attr("content")+'">'+
            '<input type="hidden" name="status" value="'+status+'">'+
            '<div class="form-body margin-top-10">'+err+
            '<div class="form-group">'+
            '<label class="control-label col-md-2">备注：</label>'+
            '<div class="col-md-9">'+
            '<input type="text" name="text" class="form-control" value="" ></div>'+
            '</div>'+
            '</form>';
        bootbox.dialog({
            message:html,
            size:'large',
            title:'保险订单审核备注',
            buttons: {
                confirm: {
                    label: '确定',
                    className: 'btn-success',
                    callback:function(result){
                        var parms=$('#order_audit').serialize();
                        App.blockUI();
                        $.post('<?= \yii\helpers\Url::to(['order/changorder']) ?>',parms,function(data){

                            App.unblockUI();
                            if(data.code == 'yes'){
                                showToastr('success',data.message);
                                setTimeout(function(){
                                    window.location.reload();
                                },1500);
                            }else{
                                showToastr('error',data.message);
                            }
                            return false;

                        });

                        return false;
                    }
                },
                cancel: {
                    label: '取消',
                    className: 'btn-danger cancel_issue'
                }
            }
        });
    }

    function updaetCoverage() {
        $('#update_coverage_modal').modal('show');
        $('#submit_coverage_order').on('click', function () {
            var number = $.trim($('#order_update_form input[name="policy_number"]').val());
            if (!number) {
                showToastr('error', '请检查您的输入');
                return false;
            }
            App.blockUI();
            $.post('<?= \yii\helpers\Url::to(['order/updatecoverage']) ?>',
                {
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                    'order_id':<?=$order['order_id']?>,
                    'number': number
                }, function (data) {
                    App.unblockUI();
                    if (data.code == 'yes') {
                        $('#update_coverage_modal').modal('hide');
                        showToastr('success', data.message);
                        setTimeout(function () {
                            window.location.reload();
                        });
                    } else {
                        showToastr('error', data.message);
                    }
                }
            );
        })
    }
</script>
