<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?= \yii\helpers\Url::to(['order/index']) ?>">维保订单</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>维保明细#ID-<?= $_REQUEST['id'] ?></span>
        </li>
    </ul>
</div>

<div class="row">

    <div class="row">
        <div class="col-md-12">
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
                                                <i class="fa fa-cogs"></i>维保明细
                                            </div>
                                                <div class="actions">
                                                </div>
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="row">
                                                <div class="col-xs-12">
													<table  class="table table-bordered table-hover">
														<thead>

														</thead>
														<tbody>

															<tr>
																<th>订单基本信息</th>
															</tr>
															<tr>
																<th class="font-grey-salsa">账户编号</th>
																<td><?= $member['member_id'] ?></td>
																<th class="font-grey-salsa">会员电话</th>
																<td><?= $member['phone'] ?></td>
																<th class="font-grey-salsa">会员名称</th>
																<td><?= $member['name'] ?></td>
															</tr>
																<tr>
																	<th class="font-grey-salsa">保险订单号</th>
																	<td><?= $order['order_sn'] ?></td>
																	<th class="font-grey-salsa">维保类型</th>
																	<td><?= $order->getTypeText() ?></td>
																	<th class="font-grey-salsa">下单日期</th>
																	<td><?= $order['add_time']?date('Y-m-d H:i',$order['add_time']):'' ?></td>
																</tr>
															<tr>
																<th>理赔客户资料</th>
															</tr>
															<tr>

																<th class="font-grey-salsa">联系人</th>
																<td><?= $order['contact'] ?></td>
																<th class="font-grey-salsa">联系电话</th>
																<td><?= $order['contact_number'] ?></td>

															</tr>
															<tr>
																<th class="font-grey-salsa">客户备注</th>
																<td><?= strip_tags($order['mark']) ?></td>
																<th class="font-grey-salsa">预约时间</th>
																<td><?= $order['appointment_date'] .' '.$order['appointment_time']?></td>
															</tr>
															<tr>

																<th class="font-grey-salsa">理赔地址</th>
																<td><?= $order->getAddressInfo() ?></td>
																<th class="font-grey-salsa">详细地址</th>
																<td><?= $order['address'] ?></td>
															</tr>
															<tr>
																<th class="font-grey-salsa">手机品牌信息</th>
																<td><?= $orderExtend->getPhoneInfo() ?></td>
																<th class="font-grey-salsa">快运单号(邮递类型)</th>
																<td><?= $order['express_number']?>
																	<?php if($express = $order->getExpressInfo()){
																		echo "(".$express['e_name'].")";
																	} ?>
																</td>
															</tr>
														<tr>
															<th class="font-grey-salsa">手机正面</th>
															<td colspan="3">
																<?php if($order['phone_img']): ?>
																<img class="show_img" width="270" height="auto" src="<?= $order['phone_img'] ?>">
																<?php endif; ?>
															</td>
														</tr>
															<tr>
																<th class="font-grey-salsa">手机背面</th>
																<td colspan="3">
																	<?php if($order['back_img']): ?>
																		<img class="show_img" width="270" height="auto" src="<?= $order['back_img'] ?>">
																	<?php endif; ?>
																</td>
															</tr>
															<tr>
																<th class="font-grey-salsa">身份证正面</th>
																<td colspan="3">
																	<?php if($order['id_face_img']): ?>
																		<img class="show_img" width="270" height="auto" src="<?= $order['id_face_img'] ?>">
																	<?php endif; ?>
																</td>
															</tr>
															<tr>
																<th class="font-grey-salsa">身份证背面</th>
																<td colspan="3">
																	<?php if($order['id_back_img']): ?>
																		<img class="show_img" width="270" height="auto" src="<?= $order['id_back_img'] ?>">
																	<?php endif; ?>
																</td>
															</tr>

															<tr>
																<th>服务商家信息</th>
															</tr>

															<tr>

																<th class="font-grey-salsa">指派时间</th>
																<td><?= $model['add_time']?date('Y-m-d H:i',$model['add_time']):'' ?></td>
																<th class="font-grey-salsa">商家理赔进度</th>
																<td class="font-yellow-casablanca"><?= $model->getStatusText() ?></td>

															</tr>
																<!--如果指派 就含商家信息处理-->

																<tr>

																	<th class="font-grey-salsa">商家名称</th>
																	<td class="font-purple-medium"><?= $seller['seller_name'] ?></td>
																	<th class="font-grey-salsa">指派备注</th>
																	<td class="font-green-meadow"><?= $model['delivery_note'] ?></td>


																</tr>
															<tr>
																<th class="font-grey-salsa">联系人</th>
																<td><?= $seller['concat'] ?></td>
																<th class="font-grey-salsa">联系电话</th>
																<td><?= $seller['concat_tel'] ?></td>

															</tr>
															<?php if(in_array($model['service_status'],[5,6,7])): ?>
															<tr>
																<th>维修理赔信息</th>
															</tr>

																<tr>

																	<th class="font-grey-salsa">商家维修意见</th>
																	<td class="font-yellow-casablanca"><?= $model['vertify_result'] ?></td>
																	<th class="font-grey-salsa">维修完成时间</th>
																	<td><?= $model['repair_ok_time']?date('Y-m-d H:i',$model['repair_ok_time']):'' ?></td>

																</tr>
																<tr>

																	<th class="font-grey-salsa">报价类型</th>
																	<td class="font-green-meadow"><?= $model->getBaojiaTypeText() ?></td>
																	<th class="font-grey-salsa">平台服务费比例</th>
																	<td class="font-red-thunderbird"><?= $model['expenses'] ?>%</td>

																</tr>
																<tr>

																	<th class="font-grey-salsa">商家理赔报价</th>
																	<td class="font-green-meadow"><?= $model['total_price'] ?></td>
																	<th class="font-grey-salsa">理赔结算=总报价*（1-平台服务费比）</th>
																	<td class="font-red-thunderbird"><?= $model['total_price'] ?>*(1-<?= $model['expenses'] ?>%)=<?= number_format($model['total_price']*(1-$model['expenses']/100),2,'.','') ?></td>

																</tr>
																<?php  foreach($model->getVerfiyImageInfo() as $item): ?>
																<tr>
																	<th class="font-grey-salsa"><?= $item['name'] ?></th>
																	<td colspan="3">
																		<?php if($item['href']): ?>
																			<img class="show_img" width="270" height="auto" src="<?= $item['href'] ?>">
																		<?php endif; ?>
																	</td>
																</tr>
															   <?php  endforeach;?>
															<?php endif; ?>

															<?php if($model['service_status'] == \common\models\OrderMaintenanceService::_MS_STATE_TO_DOOR
																|| $model['service_status'] == \common\models\OrderMaintenanceService::_MS_STATE_IN_SERVICE
															|| $model['service_status'] == \common\models\OrderMaintenanceService::_MS_STATE_TO_CHECK_FAIL
															):
																/**
																 * 如果指派单子 还在 待上门服务 服务中  或者 提交资料失败 都可以再操作
																 **/

																?>
															<tr>
																<th class="font-red-thunderbird">理赔状态更新</th>
																<td>
                                                                    <a class="btn green  sbold" data-target="#service-responsive" href="<?= \yii\helpers\Url::to(['order/showlipei','order_id'=>$order['id'],'order_service_id'=>$model['id']]) ?>" data-toggle="modal">
                                                                        <i class="fa fa-share"></i>理赔流程处理
                                                                    </a>
                                                                </td>

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

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="portlet  default  box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>维保日志
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
                                                            <th width="18%">操作人员</th>
                                                            <th>备注</th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
														<?php if($log = $order->getLogInfo(['is_show'=>1])): ?>
															<?php foreach($log as $k=>$v): ?>
                                                            <tr <?php if($k==0): ?>style="color: red"<?php endif; ?> >
																<td><?= $v['add_time'] ?></td>
                                                                <td><?= $v['name'] ?></td>
                                                                <td><?= $v['mark'] ?></td>

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
        </div>
    </div>
</div>
<!-- END PAGE LEVEL PLUGINS -->

	<!-- begin 商家理赔状态流程更新3 modal -->
<div class="modal fade bs-modal-lg" tabindex="-1" id="service-responsive" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<img src="<?= Yii::getAlias('@metro') ?>/global/img/loading-spinner-grey.gif" alt="" class="loading">
				<span> &nbsp;&nbsp;Loading... </span>
			</div>
		</div>
	</div>
</div>
	<!-- end 商家理赔状态流程更新 modal -->
