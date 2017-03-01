<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title">申领信息发放处理</h4>
</div>
<div class="modal-body">
	<div class="row">
		<form action="#" id="service_form" method="POST" class="form-horizontal">
			<div class="form-body">
				<div class="form-group">
					<label class="control-label col-md-2">
						申领商家<span class="required"> * </span>
					</label>
					<div class="col-md-6">
						<input class="form-control" type="text" readonly="" value="<?= $seller->seller_name ?> <?= $seller->concat_tel ?>" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">
						申领货号<span class="required"> * </span>
					</label>
					<div class="col-md-6">
						<input class="form-control" type="text" readonly="" value="<?= $card_pay->pay_sn ?>" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">
						付款方式<span class="required"> * </span>
					</label>
					<div class="col-md-6">
						<input class="form-control" type="text" readonly="" value="<?= \common\models\CardOrderPayback::getTypeMsg($card_pay->apply_type) ?>" placeholder="">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-2">
						险种名称<span class="required"> * </span>
					</label>
					<div class="col-md-6">

						<input class="form-control" type="text" readonly="" value="<?php if($coverage = $order->getCoverageInfo()){ echo $coverage->coverage_name; } ?>" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">
						险种编码<span class="required"> * </span>
					</label>
					<div class="col-md-6">

						<input class="form-control" type="text" readonly="" value="<?= $order->coverage_code ?>" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">
						险种数量<span class="required"> * </span>
					</label>
					<div class="col-md-6">

						<input class="form-control" type="text" readonly="" value="<?= $order->number ?>" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-2">卡券编号<span class="required"> * </span></label>
					<div class="col-md-6">
						<div class="input-icon right">
							<textarea class="form-control" rows="5" id="card_number_str" name="card_number_str"></textarea>
						</div>
						<span class="help-block font-red-pink">文本框内多个卡券用","分开,每张卡券号由7位数字构成</span>
							<span>
								或者&nbsp;<?= \common\widgets\Parsefile::widget(['id'=>'card_number_str']) ?>
							</span>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-2">发放备注
						<span class="required">* </span>
					</label>
					<div class="col-md-6">
						<textarea name="service_note" id="service_note" value=""  class="form-control" placeholder="请输入简要的备注信息" rows="3"></textarea>
					</div>
				</div>
			</div>
			<input type="hidden" name="order_id" value="<?= $order->order_id ?>">
			<input type="hidden" name="_csrf-maintainer" value="<?=Yii::$app->request->csrfToken?>">
			<div class="form-actions">
				<div class="row">
					<div class="col-md-offset-3 col-md-9">
						<button type="button" class="btn green" id="submit_lipei_liucheng" >Submit</button>
					</div>
				</div>
			</div>
		</form>


	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn default" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">

	$(function(){

		$('#submit_lipei_liucheng').on('click',function(){

			var ok = true;
			var kay_value_set = $('#service_form').serializeArray();
				$(kay_value_set).each(function(key,item){
					if(item.name=='card_number_str' && item.value==''){
						showToastr('error', '填写待发放卡券序列号');
						ok = false;
					}
					if(item.name=='service_note' && item.value==''){
						showToastr('error', '填写简要备注信息');
						ok = false;
					}
				});
				if(!ok){
					return false;
				}
				App.startPageLoading();
				$.post(
					'<?= \yii\helpers\Url::to(['card/issuemod']) ?>',
					kay_value_set,
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

		});

	});

</script>