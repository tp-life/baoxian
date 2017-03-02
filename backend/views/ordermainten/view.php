<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?= \yii\helpers\Url::to(['ordermainten/index']) ?>">维保订单</a>
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
																<th>下单账户</th>
															</tr>
															<tr>
																<th>会员编号</th>
																<th>会员电话</th>
																<th>会员名称</th>
															</tr>
															<?php if($member = $model->getMemberInfo()): ?>
																<tr>
																	<td><?= $member['member_id'] ?></td>
																	<td><?= $member['phone'] ?></td>
																	<td><?= $member['name'] ?></td>
																</tr>
															<?php endif;?>

																<tr>
																	<th>订单基本信息</th>
																</tr>
																<tr>

																	<th class="font-grey-salsa">保险订单号</th>
																	<td><?= \yii\helpers\Html::a($model['order_sn'],['order/view', 'id' => $model['order_id']],["class"=>"font-purple-medium",'title'=>'查看订单详细','target'=>'_blank']) ?></td>
																	<th class="font-grey-salsa">维保类型</th>
																	<td><?= $model->getTypeText() ?></td>
																	<th class="font-grey-salsa">维保状态</th>
																	<td><?= \yii\helpers\Html::label($model->getStateText(),null,["class"=>"font-yellow-casablanca"]) ?>
																	</td>
																	<th class="font-grey-salsa">下单日期</th>
																	<td><?= $model['add_time']?date('Y-m-d H:i',$model['add_time']):'' ?></td>
																</tr>
															<tr>
																<th>理赔客户资料</th>
															</tr>
															<tr>

																<th class="font-grey-salsa">联系人</th>
																<td><?= $model['contact'] ?></td>
																<th class="font-grey-salsa">联系电话</th>
																<td><?= $model['contact_number'] ?></td>

															</tr>
															<tr>
																<th class="font-grey-salsa">客户备注</th>
																<td><?= strip_tags($model['mark']) ?></td>
																<th class="font-grey-salsa">预约时间</th>
																<td><?= $model['appointment_date'] .' '.$model['appointment_time']?></td>
															</tr>
															<tr>

																<th class="font-grey-salsa">理赔地址</th>
																<td><?= $model->getAddressInfo() ?></td>
																<th class="font-grey-salsa">详细地址</th>
																<td><?= $model['address'] ?></td>
																<th class="font-grey-salsa">快运单号(邮递类型)</th>
																<td><?= $model['express_number']?>
																<?php if($express = $model->getExpressInfo()){
																		echo "(".$express['e_name'].")";
																	} ?>
																</td>


															</tr>
															<tr>
																<th class="font-grey-salsa">手机品牌信息</th>
																<td><?= $orderExtend->getPhoneInfo() ?></td>
																<th class="font-grey-salsa"></th>
																<td></td>

															</tr>
														<tr>
															<th class="font-grey-salsa">手机正面</th>
															<td colspan="3">
																<?php if($model['phone_img']): ?>
																<img class="show_img" width="270" height="auto" src="<?= $model['phone_img'] ?>">
																<?php endif; ?>
															</td>
														</tr>
															<tr>
																<th class="font-grey-salsa">手机背面</th>
																<td colspan="3">
																	<?php if($model['back_img']): ?>
																		<img class="show_img" width="270" height="auto" src="<?= $model['back_img'] ?>">
																	<?php endif; ?>
																</td>
															</tr>
															<tr>
																<th class="font-grey-salsa">身份证正面</th>
																<td colspan="3">
																	<?php if($model['id_face_img']): ?>
																		<img class="show_img" width="270" height="auto" src="<?= $model['id_face_img'] ?>">
																	<?php endif; ?>
																</td>
															</tr>
															<tr>
																<th class="font-grey-salsa">身份证背面</th>
																<td colspan="3">
																	<?php if($model['id_back_img']): ?>
																		<img class="show_img" width="270" height="auto" src="<?= $model['id_back_img'] ?>">
																	<?php endif; ?>
																</td>
															</tr>
															<?php $serviceInfo = $model->getServiceInfo(); ?>
															<tr>
																<th>维修商家信息<span class="font-grey-salsa">(多次指派显示最近一次)</span></th>
															</tr>
															<?php if($serviceInfo): ?>
															<tr>

																<th class="font-grey-salsa">指派时间</th>
																<td><?= $serviceInfo['add_time']?date('Y-m-d H:i',$serviceInfo['add_time']):'' ?></td>
																<th class="font-grey-salsa">商家理赔进度</th>
																<td class="font-yellow-casablanca"><?= $serviceInfo->getStatusText() ?></td>

															</tr>
																<!--如果指派 就含商家信息处理-->
																<?php if($sellerInfo = $serviceInfo->getSellerInfo()): ?>
																<tr>

																	<th class="font-grey-salsa">商家名称</th>
																	<td class="font-purple-medium"><?= \yii\helpers\Html::a($sellerInfo['seller_name'],['seller/view', 'id' => $serviceInfo['m_id']],["class"=>"font-purple-medium",'title'=>'查看商家详细','target'=>'_blank']) ?></td>
																	<th class="font-grey-salsa">联系人</th>
																	<td><?= $sellerInfo['concat'] ?></td>
																	<th class="font-grey-salsa">联系电话</th>
																	<td><?= $sellerInfo['concat_tel'] ?></td>

																</tr>
																<?php endif; ?>
																<tr>

																	<th class="font-grey-salsa">指派备注（客户不可见）</th>
																	<td class="font-green-meadow"><?= $serviceInfo['delivery_note'] ?></td>
																	<th class="font-grey-salsa">商家反馈备注（客户不可见）</th>
																	<td class="font-red-thunderbird"><?= $serviceInfo['manager_note'] ?></td>
																	<!--<th class="font-grey-salsa">客服备注（客户不可见）</th>
																	<td class="font-red-thunderbird"><?/*= $serviceInfo['server_mark'] */?></td>-->

																</tr>
															<?php else: ?>
																<tr>
																	<td class="font-red-thunderbird">暂无指派信息</td>
																</tr>
															<?php endif; ?>


															<tr>
																<th>维修商家提交资料</th>
															</tr>

															<?php if($serviceInfo && in_array($serviceInfo->service_status ,[5,6,7])): ?>
																<tr>

																	<th class="font-grey-salsa">商家维修意见</th>
																	<td class="font-yellow-casablanca"><?= $serviceInfo['vertify_result'] ?></td>
																	<th class="font-grey-salsa">维修完成时间</th>
																	<td><?= $serviceInfo['repair_ok_time']?date('Y-m-d H:i',$serviceInfo['repair_ok_time']):'' ?></td>

																</tr>
																<tr>

																	<th class="font-grey-salsa">报价类型</th>
																	<td class="font-green-meadow"><?= $serviceInfo->getBaojiaTypeText() ?></td>
																	<th class="font-grey-salsa">平台服务费比例</th>
																	<td class="font-red-thunderbird"><?= $serviceInfo['expenses'] ?>%</td>

																</tr>
																<tr>

																	<th class="font-grey-salsa">商家理赔报价</th>
																	<td class="font-green-meadow"><?= $serviceInfo['total_price'] ?></td>
																	<th class="font-grey-salsa">理赔结算=总报价*（1-平台服务费比）</th>
																	<td class="font-red-thunderbird"><?= $serviceInfo['total_price'] ?>*(1-<?= $serviceInfo['expenses'] ?>%)=<?= number_format($serviceInfo['total_price']*(1-$serviceInfo['expenses']/100),2,'.','') ?></td>

																</tr>
																<?php foreach($serviceInfo->getVerfiyImageInfo() as $item): ?>
																<tr>
																	<th class="font-grey-salsa"><?= $item['name'] ?></th>
																	<td colspan="3">
																		<?php if($item['href']): ?>
																			<img class="show_img" width="270" height="auto" src="<?= $item['href'] ?>">
																		<?php endif; ?>
																	</td>
																</tr>
															   <?php  endforeach;?>
															<?php else: ?>
																<tr>
																	<td class="font-red-thunderbird">暂无维修商家提交资料信息</td>
																</tr>
															<?php endif; ?>


															<?php if($model['state'] !=\common\models\OrderMaintenance::_MT_STATE_TO_CHECK && $model['state'] !=\common\models\OrderMaintenance::_MT_STATE_TO_APPOINT && $serviceInfo  &&
															$serviceInfo->service_status!=\common\models\OrderMaintenanceService::_MS_STATE_CHECK_SUCCESS
																	&& $serviceInfo->service_status!=\common\models\OrderMaintenanceService::_MS_STATE_FAIL &&
																	$serviceInfo->service_status!=\common\models\OrderMaintenanceService::_MS_STATE_TO_CHECK_FAIL): ?>
															<tr>
																<th class="font-red-thunderbird">理赔状态更新</th>
																<td><a class="btn green  sbold" data-target="#service-responsive" href="javascript:;" data-toggle="modal">维修处理</a></td>

															</tr>

															<?php else: ?>
																<tr>
																	<th class="font-red-thunderbird">维保状态更新</th>
																	<?php if($model['state'] == \common\models\OrderMaintenance::_MT_STATE_TO_CHECK): ?>
																		<td>
																			<a class="btn red  sbold" data-target="#state-ajax" href="javascript:;" data-toggle="modal"> 审核维保 </a>
																		</td>
																	<?php elseif($model['state'] ==\common\models\OrderMaintenance::_MT_STATE_TO_APPOINT): ?>
																		<td>
																			<a class="btn red  sbold" data-target="#zhipai-ajax" href="javascript:;" data-toggle="modal"> 维修指派 </a>
																		</td>
																		<?php elseif($serviceInfo && $model['state'] ==\common\models\OrderMaintenance::_MT_STATE_SERVICE_RE_TO_APPOINT): ?>
																		<td>
																			<a class="btn red  sbold" data-target="#zhipai-ajax" href="javascript:;" data-toggle="modal"> 维修失败再次指派 </a>
																		</td>
																	<?php endif; ?>

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
                                                            <th width="15%">操作人员</th>
                                                            <th>备注</th>
															<th width="15%">客户可见状态</th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
														<?php if($log = $model->getLogInfo()): ?>
															<?php foreach($log as $k=>$v): ?>
                                                            <tr <?php if($k==0): ?>style="color: red"<?php endif; ?> >
																<td><?= $v['add_time'] ?></td>
                                                                <td><?= $v['name'] ?></td>
                                                                <td><?= $v['mark'] ?></td>
																<td><?= $v['is_show']?'yes':'no' ?></td>

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
<?php if($model['state'] ==\common\models\OrderMaintenance::_MT_STATE_TO_CHECK): ?>
<!-- begin change state modal  1 -->
<div class="modal fade  bs-modal-lg" id="state-ajax" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">申请审核</h4>
			</div>
			<div class="modal-body modal-lg">
				<!-- BEGIN FORM-->
				<form action="#" id="change_state_form" method="POST" class="form-horizontal">
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-2">
								审核状态<span class="required"> * </span>
							</label>
							<div class="col-md-4">
								<?= \yii\helpers\Html::radioList('state',\common\models\OrderMaintenance::_MT_STATE_TO_APPOINT,[\common\models\OrderMaintenance::_MT_STATE_FAIL=>'审核失败',\common\models\OrderMaintenance::_MT_STATE_TO_APPOINT=>'审核成功']) ?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">审核建议
								<span class="required">* </span>
							</label>
							<div class="col-md-6">
								<textarea name="note" value=""  class="form-control" placeholder="审核建议" rows="3"></textarea>
							</div>
						</div>
					</div>
					<input type="hidden" name="id" value="<?= $model['id'] ?>">
					<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<button type="submit" class="btn green">确定提交</button>
							</div>
						</div>
					</div>
				</form>
				<!-- END FORM-->
				<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
				<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
				<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
				<script type="text/javascript">

					$(function(){

						var form1 = $('#change_state_form');
						form1.validate({
							errorElement: 'span', //default input error message container
							errorClass: 'help-block help-block-error', // default input error message class
							focusInvalid: false, // do not focus the last invalid input
							ignore: "",  // validate all fields including form hidden input
							messages: {
								state: {
									required: '必填项'
								},
								note: {
									required: '必填项',
									maxlength:'请输入不超过100个审核建议字符'
								}
							},
							rules: {
								state: {
									required: true

								},
								note: {
									required: true,
									maxlength:100
								}
							},

							invalidHandler: function (event, validator) { //display error alert on form submit
								//success1.hide();
								//error1.show();
								//App.scrollTo(error1, -200);
								showToastr('error','表单项验证有误');
							},

							highlight: function (element) { // hightlight error inputs
								$(element)
									.closest('.form-group').addClass('has-error'); // set error class to the control group
							},

							unhighlight: function (element) { // revert the change done by hightlight
								$(element)
									.closest('.form-group').removeClass('has-error'); // set error class to the control group
							},

							success: function (label) {
								label
									.closest('.form-group').removeClass('has-error'); // set success class to the control group
							},

							submitHandler: function (form) {
								//success1.show();
								//error1.hide();
								//showToastr('info','验证成功');//return ;
								//console.log(form1.serializeArray());return false;
								//form.submit();
								$.post(
									'<?= \yii\helpers\Url::to(['ordermainten/changestate']) ?>',
									form1.serializeArray(),
									function(e){
										console.log(e);
										if(e.code == 'yes'){
											showToastr('success', e.message,'','toast-top-right');
											window.location.reload();
										}else{
											showToastr('error', e.message,'','toast-top-right');
										}
									},
									'json'

								);
							}
						});

					})

				</script>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>
<!-- end change state modal -->
<?php endif; ?>

<!--指派商家处理2-->
<?php if($model['state'] ==\common\models\OrderMaintenance::_MT_STATE_TO_APPOINT || $model['state'] ==\common\models\OrderMaintenance::_MT_STATE_SERVICE_RE_TO_APPOINT): ?>
	<!-- begin zhipai  modal -->
	<div class="modal fade  bs-modal-lg" id="zhipai-ajax" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">订单指派</h4>
				</div>
				<div class="modal-body modal-lg">
					<form class="form" id="zhipai_form_search" >
						<input type="hidden" name="order_id" value="<?=$model['order_id']?>">
						<div class="form-body">
							<div class="form-group">
							<select name="_tp" id="_tp"  class="table-group-action-input form-control form-filter input-inline  input-sm">
								<option value="seller_name">商家名称</option>
								<option value="concat_tel">商家联系电话</option>
								<option value="concat">商家联系人</option>
							</select>
							&nbsp;&nbsp;
							<input type="text" placeholder="请输入商家名称/电话/联系人进行查询" class="input-md form-filter" name="_tp_value" id="_tp_value" size="30">
								<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
							<button id="datatable_submit" type="button" class="btn btn-sm green">
								<i class="fa fa-search"></i> 搜索
							</button>
							</div>
							<div class="form-group">
								<p class="font-yellow-casablanca">
									&nbsp;&nbsp;搜索结果：商家名称#联系人#联系电话
								</p>
                                <p class="font-yellow-casablanca">&nbsp;&nbsp;品牌机型：<?= $orderExtend->getPhoneInfo() ?></p>
							</div>
						</div>
					</form>
					<!-- BEGIN FORM-->
					<form action="#" id="zhipai_form" method="POST" class="form-horizontal">
					<div class="form-body">

						<div class="form-group">
							<label class="control-label col-md-2">商户选择
								<span class="required"> * </span>
							</label>
							<div class="col-md-6">
								<select name="m_id" id="m_id"  class="table-group-action-input form-control form-filter input-inline  input-sm">
									<option value="">搜索商家并选择指派</option>
								</select>

							</div>
						</div>
                        <div class="form-group">
                            <label class="control-label col-md-2">报修区域
                                <span class="required"> * </span>
                            </label>

                            <div class="col-md-6">

                                <select class="table-group-action-input form-control form-filter input-inline  input-sm" name="province_id"
                                        id="province_id">
                                    <option value="">请选择省</option>
									<?php foreach ($province as $key => $val): ?>
                                        <option
                                                value="<?= $val->area_id . ',' . $val->area_name ?>" ><?= $val->area_name ?></option>
									<?php endforeach ?>
                                </select>

                                <select class="table-group-action-input form-control form-filter input-inline  input-sm" name="city_id" id="city_id"
                                        >
                                    <option value="">请选择市</option>
                                </select>
                                <select class="table-group-action-input form-control form-filter input-inline  input-sm" name="area_id" id="area_id"
                                        >
                                    <option value="">请选择区</option>
                                </select>
                                <br/>
                                <font style="margin: 0px 1px 0px 0px;font-size: small" class="font-purple-medium">默认商家地址</font>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2">详细地址
                                <span class="required">*</span>
                            </label>

                            <div class="col-md-6">
                                <input id="detail_address" type="text" class="form-control" value="" name="detail_address" placeholder=""/>
                            </div>
                        </div>
						<div class="form-group">
							<label class="control-label col-md-2">指派留言
								<span class="required">* </span>
							</label>
							<div class="col-md-6">
								<textarea name="zhipai_note" value=""  class="form-control" placeholder="审核建议" rows="3"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">理赔类型
								<span class="required"></span>
							</label>
							<div class="col-md-6">
								<?= \yii\helpers\Html::dropDownList('type',$model->type,\common\models\OrderMaintenance::typeData(),['class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
								&nbsp;当前类型：<span class="required"><?= $model->getTypeText() ?> </span>
							</div>
						</div>
					</div>
					<input type="hidden" name="id" value="<?= $model['id'] ?>">
					<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<button type="submit" class="btn green">确认</button>
							</div>
						</div>
					</div>
					</form>
					<!-- END FORM-->
					<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
					<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
					<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
					<script type="text/javascript">

						$(function(){

							$('#datatable_submit').click(function(e){
								if($('#_tp_value').val().length==0){
									showToastr('info','请输入搜索关键字');return false;

								}
								App.startPageLoading();
								$.post(
									'<?= \yii\helpers\Url::to(['ordermainten/zhipaisearch']) ?>',
									$('#zhipai_form_search').serializeArray(),
									function(e){
										console.log(e);
										if(e.code == 'yes'){
											showToastr('success', e.message,'','toast-top-right');
											var optionString = '<option value="">搜索商家并选择指派</option>';
											var ct = 0;
                                            var se_val = '';
											$.each(e.data,function(key,value){
											    //console.log(key);
                                                se_val = ct==0?'selected':'';
												optionString += '<option '+se_val+' value="'+value['seller_id']+'">'+value['seller_name']+'#'+value['concat']+'#'+value['concat_tel']+'</option>';
											    ct = ct+1;
											});
											$('#m_id').empty().html(optionString);
                                            $('#m_id').trigger('change');


										}else{
											showToastr('error', e.message,'','toast-top-right');
										}
									},
									'json'

								);
								App.stopPageLoading();

							})

							var form1 = $('#zhipai_form');
							form1.validate({
								errorElement: 'span', //default input error message container
								errorClass: 'help-block help-block-error', // default input error message class
								focusInvalid: false, // do not focus the last invalid input
								ignore: "",  // validate all fields including form hidden input
								messages: {
									m_id: {
										required: '必填项'
									},
                                    province_id:{
                                        required: '必填项'
                                    },
                                    city_id:{
                                        required: '必填项'
                                    },
                                    area_id:{
                                        required: '必填项'
                                    },
                                    detail_address:{
                                        required: '必填项'
                                    },
									zhipai_note: {
										required: '必填项',
										maxlength:'请输入不超过100个审核建议字符'
									}
								},
								rules: {
									m_id: {
										required: true
									},
                                    province_id: {
                                        required: true
                                    },
                                    city_id: {
                                        required: true
                                    },
                                    area_id: {
                                        required: true
                                    },
                                    detail_address: {
                                        required: true
                                    },
									zhipai_note: {
										required: true,
										maxlength:100
									}
								},

								invalidHandler: function (event, validator) { //display error alert on form submit
									//success1.hide();
									//error1.show();
									//App.scrollTo(error1, -200);
									showToastr('error','表单项验证有误');
								},

								highlight: function (element) { // hightlight error inputs
									$(element)
										.closest('.form-group').addClass('has-error'); // set error class to the control group
								},

								unhighlight: function (element) { // revert the change done by hightlight
									$(element)
										.closest('.form-group').removeClass('has-error'); // set error class to the control group
								},

								success: function (label) {
									label
										.closest('.form-group').removeClass('has-error'); // set success class to the control group
								},

								submitHandler: function (form) {
									//success1.show();
									//error1.hide();
									//showToastr('info','验证成功');return ;
									//form.submit();
									App.startPageLoading();
									$.post(
										'<?= \yii\helpers\Url::to(['ordermainten/zhipaiseller']) ?>',
										form1.serializeArray(),
										function(e){
											console.log(e);
											if(e.code == 'yes'){
												showToastr('success', e.message,'','toast-top-right');
												window.location.reload();
											}else{
												showToastr('error', e.message,'','toast-top-right');
											}
										},
										'json'

									);
									App.stopPageLoading();
								}
							});



                            //省市区加载

                            $('#province_id,#city_id').on('change',function(){
                                getArea(this,0)
                            });


                            //商户选择事件
                            $('#m_id').on('change',function(){
                               getSellerInfo(this,function(respon){
                                    $('#province_id')[0].selectedIndex = respon.province_id_value;
                                    getArea('#province_id',respon.city_id_value);
                                   setTimeout(function () {
                                       getArea('#city_id',respon.area_id_value);
                                       $('#detail_address').val(respon.detail_address);
                                   },300)


                                });

                            });


						});


                        function getSellerInfo(obj,callback) {

                            //商家地址对象
                            var respon = {
                                province_id_value: '0',
                                city_id_value: '0',
                                area_id_value: '0',
                                area_info: '',
                                detail_address: ''
                            };
                            var seller_id = $(obj).val();
                            if (!seller_id) {
                                $('#province_id')[0].selectedIndex = 0;
                                $('#city_id')[0].selectedIndex = 0;
                                $('#area_id')[0].selectedIndex = 0;
                                $('#detail_address').val('');
                                return respon;
                            }
                            App.startPageLoading();
                            $.post('<?= \yii\helpers\Url::to(['seller/index']) ?>',
                                {
                                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                                    'leader': 'yes_and_info',
                                    'seller_id': seller_id
                                },
                                function (e) {
                                    if (e.code == 'yes') {
                                        respon = {
                                            province_id_value: e.data.province_id,
                                            city_id_value: e.data.city_id,
                                            area_id_value: e.data.area_id,
                                            area_info: e.data.area_info,
                                            detail_address: e.data.detail_address
                                        };

                                        typeof callback == 'function' && callback(respon)

                                    }
                                },'json');
                            App.stopPageLoading();
                        }

                        function getArea(obj, default_id) {
                            var default_id = default_id || 0;
                            var html = '<option value="">请选择地区</option>';
                            var area = $(obj).val();
                            if (!area) {
                                $('#city_id').html(html);
                                $('#area_id').html(html);
                                return;
                            }
                            var area_k_v = area.split(',');
                            App.startPageLoading();
                            $.post('<?= \yii\helpers\Url::to(['seller/getarea']) ?>', {
                                'id': area_k_v[0],
                                '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
                            }, function (data) {
                                if (data.code !== 'yes') {
                                    showToastr('warning', data.message);
                                    return false;
                                }
                                console.log(default_id)
                                $.each(data.data, function (index, ele) {
                                    if (ele.area_id == default_id) {
                                        html += '<option selected value="' + ele.area_id + ',' + ele.area_name + '">' + ele.area_name + '</option>';
                                    } else {
                                        html += '<option value="' + ele.area_id + ',' + ele.area_name + '">' + ele.area_name + '</option>';
                                    }
                                })
                                $(obj).attr('name') == 'province_id' ? $('#city_id').html(html) : $('#area_id').html(html);
                                App.stopPageLoading();
                            }, 'json');
                        }


					</script>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>
	<!-- end 指派商家处理 modal -->
<?php endif; ?>



<?php if($serviceInfo = $model->getServiceInfo()): ?>
	<!-- begin 商家理赔状态流程更新3 modal -->
	<div class="modal fade  bs-modal-lg" id="service-responsive" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">理赔状态处理</h4>
				</div>
				<div class="modal-body modal-lg">
					<!-- BEGIN FORM-->
					<form action="#" id="service_form" method="POST" class="form-horizontal">
						<div class="form-body">
							<div class="form-group">
								<label class="control-label col-md-2">
									理赔状态<span class="required"> * </span>
								</label>
								<div class="col-md-4">
									<?= \yii\helpers\Html::radioList('service_status',$model['state'],\common\models\OrderMaintenanceService::serviceStateData()) ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2">理赔备注
									<span class="required">* </span>
								</label>
								<div class="col-md-6">
									<textarea name="service_note" value=""  class="form-control" placeholder="理赔备注" rows="3"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2">可见备注
									<span class="required"></span>
								</label>
								<div class="col-md-4">
									<div class="checkbox-list">
										<input type="checkbox" checked name="is_show" value="1" id="is_show">备注对客户可见
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" name="m_order_id" value="<?= $model['id'] ?>">
						<input type="hidden" name="m_order_service_id" value="<?= $serviceInfo['id'] ?>">
						<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
						<div class="form-actions">
							<div class="row">
								<div class="col-md-offset-3 col-md-9">
									<button type="submit" class="btn green">确认提交</button>
								</div>
							</div>
						</div>
					</form>
					<!-- END FORM-->
					<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
					<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
					<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
					<script type="text/javascript">

						$(function(){

							var form1 = $('#service_form');
							form1.validate({
								errorElement: 'span', //default input error message container
								errorClass: 'help-block help-block-error', // default input error message class
								focusInvalid: false, // do not focus the last invalid input
								ignore: "",  // validate all fields including form hidden input
								messages: {
									service_status: {
										required: '必填项'
									},
									service_note: {
										required: '必填项',
										maxlength:'请输入不超过100个字符'
									}
								},
								rules: {
									service_status: {
										required: true

									},
									service_note: {
										required: true,
										maxlength:100
									}
								},

								invalidHandler: function (event, validator) { //display error alert on form submit
									//success1.hide();
									//error1.show();
									//App.scrollTo(error1, -200);
									showToastr('error','表单项验证有误');
								},

								highlight: function (element) { // hightlight error inputs
									$(element)
										.closest('.form-group').addClass('has-error'); // set error class to the control group
								},

								unhighlight: function (element) { // revert the change done by hightlight
									$(element)
										.closest('.form-group').removeClass('has-error'); // set error class to the control group
								},

								success: function (label) {
									label
										.closest('.form-group').removeClass('has-error'); // set success class to the control group
								},

								submitHandler: function (form) {
									//success1.show();
									//error1.hide();
									//showToastr('info','验证成功');//return ;
									//console.log(form1.serializeArray());return false;
									//form.submit();
									$.post(
										'<?= \yii\helpers\Url::to(['ordermainten/lipei']) ?>',
										form1.serializeArray(),
										function(e){
											//console.log(e);return;
											if(e.code == 'yes'){
												showToastr('success', e.message,'','toast-top-right');
												window.location.reload();
											}else{
												showToastr('error', e.message,'','toast-top-right');
											}
										},
										'json'

									);
								}
							});

						})

					</script>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>
	<!-- end 商家理赔状态流程更新 modal -->
<?php endif; ?>
