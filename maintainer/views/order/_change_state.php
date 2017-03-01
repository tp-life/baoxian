<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title">维保理赔处理</h4>
</div>
<div class="modal-body">
	<div class="row">
			<form action="#" id="service_form" method="POST" class="form-horizontal">
				<div class="form-body">
					<div class="form-group">
						<label class="control-label col-md-2">
							理赔机型<span class="required"> * </span>
						</label>
						<div class="col-md-6">
							<input class="form-control" type="text" readonly="" placeholder="<?= $orderExtend->getPhoneInfo() ?>">
						</div>
					</div>
					<?php if(empty($data)): ?>
					<div class="form-group">
						<label class="control-label col-md-2">
							无报价提醒<span class="required"> * </span>
						</label>
						<div class="col-md-6">
							<span class="form-control font-red-thunderbird">无对应品牌机型报价</span>
						</div>
					</div>
						<script type="text/javascript">
							top.showToastr('error', '无对应品牌机型报价,请设置报价','','toast-top-right');
						</script>
					<?php exit(0); ?>
					<?php endif; ?>
					<div class="form-group">
						<label class="control-label col-md-2">理赔类型
							<span class="required">*</span>
						</label>
						<div class="col-md-6">
							<?= \yii\helpers\Html::dropDownList('type',$order->type,\common\models\OrderMaintenance::typeData(),['class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
							&nbsp;当前类型：<span class="required"><?= $order->getTypeText() ?> </span>
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
											value="<?= $val->area_id . ',' . $val->area_name ?>" <?= $val->area_id == $seller['province_id'] ? 'selected' : '' ?>><?= $val->area_name ?></option>
									<?php endforeach ?>
								</select>

								<select class="table-group-action-input form-control form-filter input-inline  input-sm" name="city_id" id="city_id"
										style="display: <?= $city_html ? 'inline' : 'none' ?>">
									<?= $city_html ?>
								</select>
								<select class="table-group-action-input form-control form-filter input-inline  input-sm" name="area_id" id="area_id"
										style="display: <?= $area_html ? 'inline' : 'none' ?>">
									<?= $area_html ?>
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
							<input id="detail_address" type="text" class="form-control" value="<?= $seller['detail_address'] ?>" name="detail_address" placeholder=""/>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-md-2">
							理赔状态<span class="required"> * </span>
						</label>
						<div class="col-md-8">
							<?= \yii\helpers\Html::radioList('service_status',1,\common\models\OrderMaintenanceService::showSellerState(),['separator'=>'<br/>','encode'=>false]) ?>
						</div>
					</div>
					<?php if($data): ?>
						<div class="form-group verfiy_list express_list" style="display: none">
							<label class="control-label col-md-2">

							</label>
							<div class="col-md-8">
								<font class="font-purple-medium">当理赔时邮递方式进行时，请完善快递公司及运单信息</font>
							</div>
						</div>
						<div class="form-group verfiy_list express_list" style="display: none">
							<label class="control-label col-md-2">
								快递公司<span class="required"> </span>
							</label>
							<div class="col-md-8">
								<?= \yii\helpers\Html::dropDownList('express_id',$order['express_id'],$express_list,['prompt'=>'请选择']) ?>
							</div>
						</div>
						<div class="form-group verfiy_list express_list" style="display: none">
							<label class="control-label col-md-2">
								快递单号<span class="required"> </span>
							</label>
							<div class="col-md-6">
								<input type="text" class="form-control" value="<?= $order['express_number'] ?>" name="express_number" placeholder="理赔类型为邮递方式请填写"/>
							</div>
						</div>
					<div class="form-group verfiy_list" style="display: none">
						<label class="control-label col-md-2">
							报价选项<span class="required"> * </span>
						</label>
						<div class="col-md-8">
							<div>
								<table class="table table-bordered  table-hover">
									<thead>
									<th></th>
									<th>报价编号</th>
									<th>报价名称</th>
									<!--<th>内屏</th>
									<th>外屏</th>-->
									<th>维修报价</th>
									<th>服务费比</th>
									</thead>
									<tbody>
									<?php foreach($data as $b_item): ?>
										<tr>
											<td><label><input type="radio" checked="" value="<?= $b_item['offer_id'] ?>" name="offer_info"></label></td>
											<td><?= $b_item['offer_id'] ?></td>
											<td><?= $b_item['name'] ?></td>
											<!--<td><?/*= $b_item['inner_screen'] */?></td>
											<td><?/*= $b_item['outer_screen'] */?></td>
											<td><?/*= number_format($b_item['inner_screen']+$b_item['outer_screen'],'2','.','') */?></td>-->
                                            <!--<td>待报价</td>
                                            <td>待报价</td>-->
                                            <td>待报价</td>
											<td><?= $b_item['commission'] ?>%</td>

										</tr>

									<?php endforeach; ?>

									</tbody>
								</table>


							</div>
						</div>
					</div>
					<div class="form-group verfiy_list" style="display: none">
                        <?php if(false): ?>
						<label class="control-label col-md-2">
							报价类型<span class="required"> * </span>
						</label>
						<div class="col-md-8">
							<?= \yii\helpers\Html::radioList('damage_type',3,\common\models\OrderMaintenanceService::baojiaType(),['separator'=>'<br/>','encode'=>false]) ?>
						</div>
                        <?php endif; ?>
                        <label class="control-label col-md-2">
                            维修报价<span class="required"> * </span>
                        </label>
                        <div class="col-md-6">
                            <input type="number" step="0.01" min="1" max="10000" onkeyup="if(!/^\d{1,}$/.test(this.value)){showToastr('error','请保留整数','警告');this.value='';}" class="form-control" value="<?= $order['express_number'] ?>" name="m_price" id="m_price" placeholder="维修报价精确到两位小数"/>
                        <p><font class="font-purple-medium">维修报价请填写整数</font></p>
                        </div>
					</div>
					<?php endif; ?>
					<?php if($verfiyImage = $model->getVerfiyImageInfo()): ?>
						<?php foreach($verfiyImage as $key=>$item): ?>
					<div class="form-group verfiy_list" style="display: none">
						<label class="control-label col-md-2">
							 <a class="change_pic btn" data-key="<?= $key ?>"  title="点击上传更新">上传更新</a>

							<span class="required"> * </span>
						</label>
						<div class="col-md-6">

							<div class="mt-element-ribbon bg-grey-steel">
								<div
									class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase">
									<div class="ribbon-sub ribbon-clip"></div>
									<?= $item['name'] ?>
								</div>
								<p>
									<img width="270" height="130" id="image_<?= $key ?>" class="show_img" src="<?= $item['href'] ? $item['href'] : Yii::getAlias('@image') . '/default.png' ?>"/>

								</p>
								<input type="hidden" id="<?= $key ?>" name="<?= $key ?>" value="<?= $item['href'] ?>">
							</div>

						</div>
					</div>
							<?php endforeach; ?>
					<?php endif; ?>
					<div class="form-group">
						<label class="control-label col-md-2">理赔备注
							<span class="required">* </span>
						</label>
						<div class="col-md-6">
							<textarea name="service_note" value=""  class="form-control" placeholder="请输入相应状态下简要的理赔备注信息" rows="3"></textarea>
						</div>
					</div>
				</div>
				<input type="hidden" name="m_order_id" value="<?= $order['id'] ?>">
				<input type="hidden" name="m_order_service_id" value="<?= $model['id'] ?>">
				<input type="hidden" name="_csrf-maintainer" value="<?=Yii::$app->request->csrfToken?>">
				<div class="form-actions">
					<div class="row">
						<div class="col-md-offset-3 col-md-9">
							<button type="button" class="btn green" id="submit_lipei_liucheng" >确定</button>
						</div>
					</div>
				</div>
			</form>

		<form method="post" enctype="multipart/form-data">
			<input type="file" name="UploadForm[file]" style="display: none;" id="order_view_input">
		</form>

	</div>
</div>
<div class="modal-footer">
	<!--<button type="button" class="btn default" data-dismiss="modal">Close</button>
	<button type="button" class="btn blue">Save changes</button>-->
</div>
<script src="<?= Yii::getAlias('@js'); ?>/ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript">

	$(function(){


		$('#province_id , #city_id').on('change', function () {
			var province = $(this).val();
			var pval = province.split(',');
			var name = this.name;
			$.post('<?=Yii::$app->urlManager->createUrl("account/getarea")?>', {
				'id': pval[0],
				'_csrf-maintainer': $('meta[name="csrf-token"]').attr("content")
			}, function (data) {
				data = typeof data == 'string' ? $.parseJSON(data) : data;
				if (data.code !== 'yes') {
					showToastr('warning', data.message);
					return false;
				}
				var html = '<option value="">请选择地区</option>';
				$.each(data.data, function (index, ele) {
					html += '<option value="' + ele.area_id + ',' + ele.area_name + '">' + ele.area_name + '</option>';
				})
				if (name == 'province_id') {
					$('#city_id').html(html).css('display', 'inline');
					$('#area_id').css('display', 'none');
				} else if (name == 'city_id') {
					$('#area_id').html(html).css('display', 'inline');
				}
			});
		});


		$("input[name='service_status']").bind('click',function (){

				console.log($(this).val());

				//理赔资料提交
				if($(this).val()=='<?= \common\models\OrderMaintenanceService::_MS_STATE_INFO_TO_BE_SUBMIT ?>'){
					$('.verfiy_list').css('display','');
				}
				//理赔服务中
				else if($(this).val()=='<?= \common\models\OrderMaintenanceService::_MS_STATE_IN_SERVICE ?>'){
					$('.verfiy_list').css('display','none');
					$('.express_list').css('display','');
				}
				else{
					$('.verfiy_list').css('display','none');
					$('.express_list').css('display','none');
				}
			}
		);


		var obj, key;
		$('.change_pic').on('click', function () {
			obj = this;
			key = $(this).attr('data-key');
			$('#order_view_input').click();
		});

		$('#order_view_input').on('change', function () {
			var src = getFullPath($(this)[0]);
			$('#image_'+key).attr('src', src);

			$.ajaxFileUpload({
				url: '<?= \yii\helpers\Url::to(['order/upload']) ?>',
				secureuri: false,
				fileElementId: 'order_view_input',
				data: {
					'_csrf-maintainer': "<?=Yii::$app->request->csrfToken?>",
					'order_id':<?=$order['id']?>,
					'key': key
				},
				dataType: 'json',
				success: function (data,status) {
					console.log(data);
					//console.log(status);
					if(data.code=='yes'){
						$('#'+data.data.key).val(data.data.url);
						$('#image_'+key).attr('src', data.data.url);
						showToastr('success',data.message);
					}else{
						showToastr('error',data.message);
                        $('#image_'+key).attr('src', '');
					}

				},
				error: function (data, status, e) {
					showToastr('error', e);
				}
			})

		});

		$('#submit_lipei_liucheng').on('click',function(){
			var province_id = $('#province_id').val();
			var city_id = $('#city_id').val();
			var area_id = $('#area_id').val();
			var detail_address = $('#detail_address').val();

			if(!province_id ||!city_id || !area_id || !detail_address){
				showToastr('error', '请选择并填写报修地址详细');return false;
			}


			var kay_value_set = $('#service_form').serializeArray();
			var ck = false;
			var ck_ok = true;
			$(kay_value_set).each(function(key,item){
				if(item.name=='service_status' && item.value=='<?= \common\models\OrderMaintenanceService::_MS_STATE_INFO_TO_BE_SUBMIT ?>'){
					ck = true;
				}
			});
			if(ck){
				$(kay_value_set).each(function(key,item){
					console.log(key+'|'+item.name+'|'+item.value);
					if(item.name.match("image") && item.value==''){
						ck_ok = false;
						showToastr('error', '请上传图片#'+item.name);return false;
					}else if(item.name.match("price") && !(/^\d{1,}$/.test(item.value))){
                        ck_ok = false;
                        showToastr('error', '请填写维修报价');return false;
                    }

				});
			}
			$(kay_value_set).each(function(key,item){

				if(item.name=='service_note' && item.value==''){
					ck_ok = false;
					showToastr('error', '填写简要备注信息');return false;
				}
			});


			if(ck_ok){
				App.startPageLoading();
				$.post(
					'<?= \yii\helpers\Url::to(['order/dolipei']) ?>',
					kay_value_set,
					function(e){
						console.log(e);
						if(e.code == 'yes'){
							showToastr('success', e.message,'');
							window.location.reload();
						}else{
							showToastr('error', e.message,'');
						}
					},
					'json'

				);
				App.stopPageLoading();
			}
			kay_value_set = null;

		})


	})

</script>
