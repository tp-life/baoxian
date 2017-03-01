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

                                                                    <th class="font-grey-salsa">本批次已发放卡券总价值</th>
                                                                    <td class="font-red-thunderbird"><?= $pay_info->send_total_price; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="font-grey-salsa">本批次已收金额</th>
                                                                    <td class="font-red-thunderbird"><?= $pay_info->received_price; ?></td>

                                                                    <th class="font-grey-salsa">本批次退卡总金额</th>
                                                                    <td class="font-red-thunderbird"><?= $pay_info->back_price; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="font-grey-salsa">本次退回总额</th>
                                                                    <td class="font-red-thunderbird"><?= $model->total_price; ?></td>

                                                                    <th class="font-grey-salsa">本次应退金额</th>
                                                                    <td class="font-red-thunderbird"><?= $model->back_price; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="font-grey-salsa">退回数量</th>
                                                                    <td class=""><?= $model->number; ?></td>

                                                                    <th class="font-grey-salsa">申领退回商家</th>
                                                                    <td>
                                                                        <?php $seller = $model->getFromSellerInfo(); ?>
                                                                        <?php if($seller){
                                                                            echo \yii\helpers\Html::a($seller->seller_name.'['.$seller->concat_tel.']',['seller/view','id'=>$seller->seller_id],['target'=>'__blank']);
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
																	<th class="font-grey-salsa">卡券状态</th>
																	<th class="font-grey-salsa">保险名称</th>
																	<th class="font-grey-salsa">所属保险类型</th>
																	<th class="font-grey-salsa">所属保险公司</th>
																</tr>
																<?php if($card = $model->getCardInfo()): ?>
																	<?php foreach($card as $item): ?>
																<tr>
																	<td class=""><?= $item->card_number; ?></td>
																	<td class=""><?= $item->coverage_code?></td>
																	<td class="font-red-thunderbird"><?= $item->getStatusText() ?></td>
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
                                                                    <th class="font-grey-salsa">卡券编号(密钥)</th>
                                                                    <th class="font-grey-salsa">险种编码</th>
                                                                    <th class="font-grey-salsa">卡券状态</th>
                                                                    <th class="font-grey-salsa">保险名称</th>
                                                                    <th class="font-grey-salsa">所属保险类型</th>
                                                                    <th class="font-grey-salsa">所属保险公司</th>
                                                                </tr>
                                                                <?php if($card = $model->getErrCardInfo()): ?>
                                                                    <?php foreach($card as $item): ?>
                                                                        <tr>
                                                                            <td class=""><?= $item->card_number; ?>(<?= $item->card_secret; ?>)</td>
                                                                            <td class=""><?= $item->coverage_code?></td>
                                                                            <td class="font-red-thunderbird"><?= $item->getStatusText() ?></td>
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

																<?php if($model->status === \common\models\CardRefund::_RF_STATE_TO_DO): ?>
																<tr>
																	<th>退回处理</th>
																</tr>
                                                                    <tr>
                                                                        <th class="font-grey-salsa text-right">问题卡券：</th>
                                                                        <td colspan="2">
                                                                            <textarea rows="3" class="form-control" name="err_cards" id="err_cards"></textarea>
                                                                            <span class="help-block font-red-pink">单个卡券用","分开</span>
                                                                            <span>
                                                                                或者&nbsp;<?= \common\widgets\Parsefile::widget(['id'=>'err_cards']) ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
																<tr>
																	<td></td>
																	<td>
																		<?= \yii\helpers\Html::a('确认','#',['class'=>'btn font-green-sharp sbold','title'=>'确认','id'=>'rf_confirm_done']) ?>
																	</td>
																	<td>

																		<?= \yii\helpers\Html::a('退回取消','#',['class'=>'btn font-red-thunderbird sbold','title'=>'退回取消','id'=>'rf_confirm_cancel']) ?>
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
			bootbox.prompt({
				size: "large",
				title: "请填写确认并且处理备注?",
				callback: function(result){
					if(result){
						refundCardConfirm('yes',result);
					}
				}
			})
		});

		$('#rf_confirm_cancel').bind('click',function(){
			bootbox.prompt({
				size: "large",
				title: "请填写退回取消备注?",
				callback: function(result){
					if(result){
						refundCardConfirm('no',result);
					}
				}
			})
		});


	})

	function refundCardConfirm(status,note)
	{
//		App.startPageLoading();
        var err_code =$('#err_cards').val();
		$.post(
			'<?= \yii\helpers\Url::to(['cardrefund/changestate']) ?>',
			{
				'_csrf-backend':'<?= Yii::$app->request->csrfToken ?>',
				'status':status,
				'note':note,
				'refund_id':'<?= $model->id; ?>',
                'err_card':err_code
			},
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
