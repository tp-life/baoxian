
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?= \yii\helpers\Url::to(['order/index']) ?>">订单列表</a>
			<i class="fa fa-circle"></i>
		</li>
		<li>
			<span>订单编辑</span>
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="note note-danger">
			<p>• <span style="color: red">订单编辑只对系统角色开放</span></p>
			<p>• <span style="color: red">订单编辑非系统管理人员谨慎操作</span></p>
			<p>• <span style="color: red">订单编辑只允许系统管理人员角色在订单 审核失败和待完善资料状态 下可帮客户完善资料</span></p>
		</div>
		<!-- Begin: life time stats -->
		<div class="portlet light portlet-fit portlet-datatable bordered">
			<table  class="table table-bordered table-hover">
				<thead>

				</thead>
				<tbody>
				<tr>
					<th>保单信息</th>
				</tr>
				<tr>

					<th class="font-grey-salsa">订单号</th>
					<td><?= $order['order_sn'] ?></td>
					<th class="font-grey-salsa">商家名称</th>
					<td><?= $order['seller_name'] ?></td>
					<th class="font-grey-salsa">订单状态</th>
					<td class="font-purple-seance">  <?= $status ?></td>

				</tr>
				<tr>
					<th class="font-grey-salsa">支付方式</th>
					<td><?= \common\library\helper::orderPaymentName($order['payment_code']) ?></td>
					<th class="font-grey-salsa">投保金额</th>
					<td> <?= $order['order_amount'] ?></td>
					<th class="font-grey-salsa">添加时间</th>
					<td><?= date('Y-m-d H:i', $order['add_time']) ?></td>
				</tr>
				<?php if($order['payment_code'] =='kaquan' && ($cardInfo = \common\models\CardCouponsGrant::getInfoByOrder($order['order_id']))): ?>
					<tr>
						<th>卡券信息</th>
					</tr>
					<tr>

						<th class="font-grey-salsa">卡券编号(秘钥)</th>
						<td><?= $cardInfo['card_number'].'（'.$cardInfo['card_secret'].'）' ?></td>
						<th class="font-grey-salsa">所属商家</th>
						<td> <?= $cardInfo['seller_name'] ?></td>
						<td class="font-grey-salsa">联系方式</td>
						<th><?=$cardInfo['concat_tel']?></th>
					</tr>
				<?php endif; ?>

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


       </tbody>
				</table>

				<form id="edit_form" class="form-horizontal" method="post"  method="POST" action="<?=\yii\helpers\Url::to(['order/edit','id'=>$order['order_id']]) ?>">

				<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
				<input type="hidden" name="order_id" value="<?=$order['order_id']?>">

				<div class="form-body">


					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">手机品牌
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<select class="form-control" name="brand_id" id="brand_id">
									<option value="">请选择品牌</option>
									<?php foreach ($brand as $key => $val): ?>
										<option
											value="<?= $val->id . ',' . $val->model_name ?>" <?= $val->id == $order['brand_id'] ? 'selected' : '' ?>><?= $val->model_name ?></option>
									<?php endforeach ?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20"  id="model_c">
						<label class="control-label col-md-3">手机型号
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<select class="form-control" name="model_id" id="model_id">
									<option value="">请选择型号</option>
									<?= $model_html ?>
								</select>

							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">投保人姓名
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<i class="fa"></i>
								<input type="text" class="form-control" placeholder="投保人姓名限中文名2-5个汉字" name="buyer" value="<?=$order['buyer']?>"  />

							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">投保人电话
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<i class="fa"></i>
								<input type="text" class="form-control" placeholder="投保人11位有效手机号码"  name="buyer_phone" value="<?=$order['buyer_phone']?>"  />

							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">手机IMEI
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<i class="fa"></i>
								<input type="text" class="form-control" placeholder="IMEI标准格式" name="imei_code" value="<?=$order['imei_code']?>"  />

							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">身份证件号
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<i class="fa"></i>
								<input type="text" class="form-control" placeholder="有效身份证件号码" name="idcrad" value="<?=$order['idcrad']?>"  />

							</div>
						</div>
					</div>

					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">手机正面照<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<div >
									<input type="hidden" name="imei_face_image" id="coverage_image" value="<?=$order['imei_face_image']?>"  >
									<img width="300" height="400" class="show_img" id="uploadPic" style="border: 1px solid #cccccc;cursor: pointer" src="<?=$order['imei_face_image']?$order['imei_face_image']:Yii::getAlias('@image').'/upload.png'?>">

                                    <span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
								</div>
								<div class="col-md-3" style="margin-top: 5px;">
									<?=\common\widgets\Upload::widget(['id'=>'coverage_image','img'=>'uploadPic','name'=>'coverage'])?>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20">
						<label class="control-label col-md-3">手机背面照<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<div >
									<input type="hidden" name="imei_back_image" id="coverage_image_back" value="<?=$order['imei_back_image']?>"  >
									<img width="300" height="400" class="show_img" id="uploadPicBack" style="border: 1px solid #cccccc;cursor: pointer" src="<?=$order['imei_back_image']?$order['imei_back_image']:Yii::getAlias('@image').'/upload.png'?>">

                                    <span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
								</div>
								<div class="col-md-3" style="margin-top: 5px;">
									<?=\common\widgets\Upload::widget(['id'=>'coverage_image_back','img'=>'uploadPicBack','name'=>'coverage'])?>
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions margin-top-20">
                        <hr>
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<a  id="order_edit_form_submit" class="btn red">确认编辑订单提交</a>
							</div>
						</div>
					</div>
			</form>
			<p></p>
		</div>
	</div>
</div>

<script type="text/javascript">

	$('#brand_id').on('change', function () {
		var province = $(this).val();
		if(province == ''){
			return ;
		}
		var pval = province.split(',');
		App.startPageLoading();
		$.post('<?=Yii::$app->urlManager->createUrl('offer/getbrand')?>', {
			'id': pval[0],
			'_csrf-backend': $('meta[name="csrf-token"]').attr("content")
		}, function (data) {
			App.stopPageLoading();
			data = typeof data == 'string' ? $.parseJSON(data) : data;
			if (data.code !== 'yes') {
				showToastr('warning', data.message);
				return false;
			}
			var html = '<option value="">请选择型号</option>';
			$.each(data.data, function (index, ele) {
				html += '<option value="' + ele.id + ',' + ele.model_name + '">' + ele.model_name + '</option>';
			});
			$('#model_id').html(html);

		});
	});

    $('#order_edit_form_submit').on('click',function(){

        var kay_value_set = $('#edit_form').serializeArray();

        var ck_ok = true;
        $(kay_value_set).each(function(key,item){
            console.log(key+'|'+item.name+'|'+item.value);
            if(item.name=='brand_id'){
                if(item.value=='' || item.value=='0')
                {
                    ck_ok = false;
                    showToastr('error', '请选择手机品牌');return false;
                }
            }
            if(item.name=='model_id'){
                if(item.value=='' || item.value=='0')
                {
                    ck_ok = false;
                    showToastr('error', '请选择手机型号');return false;
                }
            }
            if(item.name=='buyer'){
                var reg = /[^u4E00-u9FA5]{2,5}/g;
                if(item.value=='' )
                {
                    ck_ok = false;
                    showToastr('error', '投保人非空');return false;
                }else if(!reg.test(item.value)){
                    ck_ok = false;
                    showToastr('error', '请输入中文投保人姓名');return false;
                }
            }
            if(item.name=='buyer_phone'){
                var reg =  /^1[34578]{1}[0-9]{9}$/;

                if(item.value=='' )
                {
                    ck_ok = false;
                    showToastr('error', '电话号码非空');return false;
                }else if(!reg.test(item.value)){
                    ck_ok = false;
                    showToastr('error', '电话格式错误');return false;
                }
            }
            if(item.name=='imei_code'){
                var reg = /[\a-\z\A-\Z0-9]{15,20}/g;

                if(item.value=='' || !reg.test(item.value)){
                    ck_ok = false;
                    showToastr('error', 'IMEI格式在15-20位数字或字符');return false;
                }
            }
            if(item.name=='idcrad'){
                var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                if(item.value=='' || !reg.test(item.value))
                {
                    ck_ok = false;
                    showToastr('error', '身份证件号格式不对');return false;
                }
            }
            if(item.name=='imei_face_image' && item.value==''){

                    ck_ok = false;
                    showToastr('error', '请上传手机正面照');return false;
            }
            if(item.name=='imei_back_image' && item.value==''){

                ck_ok = false;
                showToastr('error', '请上传手机背面照');return false;
            }

        });
        if(ck_ok){
            App.startPageLoading();
            $.post(
                '<?=\yii\helpers\Url::to(['order/edit','id'=>$order['order_id']]) ?>',
                kay_value_set,
                function(e){
                    console.log(e);
                    if(e.code == 'yes'){
                        showToastr('success', e.message,'');
                        setTimeout(" window.location.href='<?= \yii\helpers\Url::to(['order/view','id'=>$order['order_id']]) ?>'", 3000 );
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



</script>