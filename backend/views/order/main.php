
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?= \yii\helpers\Url::to(['order/index']) ?>">订单列表</a>
			<i class="fa fa-circle"></i>
		</li>
		<li>
			<span>申请理赔</span>
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="note note-danger">
			<p>• <span style="color: red">申请理赔只对系统角色开放</span></p>
			<p>• <span style="color: red">申请理赔非系统管理人员谨慎操作</span></p>
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
				<tr>
					<th class="font-grey-salsa">手机品牌</th>
					<td><?= $brand ?></td>
					<th class="font-grey-salsa">投保人姓名</th>
					<td> <?= $order['buyer'] ?></td>
					<th class="font-grey-salsa">投保人电话</th>
					<td><?= $order['buyer_phone'] ?></td>
				</tr>
				<tr>
					<th class="font-grey-salsa">手机IMEI</th>
					<td><?=$order['imei_code']?></td>
					<th class="font-grey-salsa">身份证件号</th>
					<td> <?= $order['idcrad'] ?></td>
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

				<form id="edit_form" class="form-horizontal" method="post"  method="POST">

				<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
				<input type="hidden" name="order_id" value="<?=$order['order_id']?>">

				<div class="form-body">
					<div class="form-group  margin-top-20">
						<div class="col-sm-4">
							<label class="control-label col-md-3">手机正面照<span class="required"> * </span>
							</label>

							<div class="col-md-8">
								<div class="input-icon right">
									<div >
										<input type="hidden" name="phone_img" id="phone_img" value=""  >
										<img width="150" height="200"  id="uploadPic_phone_img" style="border: 1px solid #cccccc;cursor: pointer" src="<?=Yii::getAlias('@image').'/upload.png'?>">

										<span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
									</div>
									<div class="col-md-3" style="margin-top: 5px;">
										<?=\common\widgets\Upload::widget(['id'=>'phone_img','img'=>'uploadPic_phone_img','name'=>'maintain'])?>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<label class="control-label col-md-3">手机背面照<span class="required"> * </span>
							</label>

							<div class="col-md-8">
								<div class="input-icon right">
									<div >
										<input type="hidden" name="back_img" id="back_img" value=""  >
										<img width="150" height="200"  id="uploadPicBack" style="border: 1px solid #cccccc;cursor: pointer" src="<?=Yii::getAlias('@image').'/upload.png'?>">

										<span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
									</div>
									<div class="col-md-3" style="margin-top: 5px;">
										<?=\common\widgets\Upload::widget(['id'=>'back_img','img'=>'uploadPicBack','name'=>'maintain'])?>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="form-group  margin-top-20">
						<div class="col-sm-4">
							<label class="control-label col-md-3">投保人身份证反面照<span class="required"> * </span>
							</label>

							<div class="col-md-8">
								<div class="input-icon right">
									<div >
										<input type="hidden" name="id_back_img" id="id_back_img" value=""  >
										<img width="150" height="200"  id="uploadPic_id_back_img" style="border: 1px solid #cccccc;cursor: pointer" src="<?=Yii::getAlias('@image').'/upload.png'?>">
										<span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
									</div>
									<div class="col-md-3" style="margin-top: 5px;">
										<?=\common\widgets\Upload::widget(['id'=>'id_back_img','img'=>'uploadPic_id_back_img','name'=>'maintain'])?>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<label class="control-label col-md-3">投保人身份证正面照<span class="required"> * </span>
							</label>

							<div class="col-md-8">
								<div class="input-icon right">
									<div >
										<input type="hidden" name="id_face_img" id="id_face_img" value=""  >
										<img width="150" height="200" class="id_face_img" id="uploadPic_id_face_img" style="border: 1px solid #cccccc;cursor: pointer" src="<?=Yii::getAlias('@image').'/upload.png'?>">
										<span class="help-inline">
                                        5M以下，jpg、png、gif等格式图片
                                    </span>
									</div>
									<div class="col-md-3" style="margin-top: 5px;">
										<?=\common\widgets\Upload::widget(['id'=>'id_face_img','img'=>'uploadPic_id_face_img','name'=>'maintain'])?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group  margin-top-20">
						<label class="control-label col-md-2">问题描述
						</label>

						<div class="col-md-4">
							<div class="input-icon right">
								<i class="fa"></i>
								<textarea class="form-control" rows="3" name="remark"></textarea>
							</div>
						</div>
					</div>
					<div class="form-actions margin-top-20">
                        <hr>
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<a  id="order_edit_form_submit" class="btn red">确认申请理赔</a>
							</div>
						</div>
					</div>
			</form>
			<p></p>
		</div>
	</div>
</div>

<script type="text/javascript">


    $('#order_edit_form_submit').on('click',function(){
        var kay_value_set = $('#edit_form').serializeArray();
        var ck_ok = true;
        $(kay_value_set).each(function(key,item){
            console.log(key+'|'+item.name+'|'+item.value);
            if(item.name=='phone_img' && item.value==''){
				ck_ok = false;
				showToastr('error', '请上传手机正面照');return false;
            }
            if(item.name=='back_img' && item.value==''){
                ck_ok = false;
                showToastr('error', '请上传手机背面照');return false;
            }
			if(item.name=='id_back_img' && item.value==''){
				ck_ok = false;
				showToastr('error', '请上传手机投保人身份证反面照');return false;
			}
			if(item.name=='id_face_img' && item.value==''){
				ck_ok = false;
				showToastr('error', '请上传投保人身份证正面照');return false;
			}
        });
        if(ck_ok){
            App.startPageLoading();
            $.post(
                '<?=\yii\helpers\Url::to(['order/maintainer','id'=>$order['order_id']]) ?>',
                kay_value_set,
                function(e){
                    console.log(e);
                    if(e.code == 'yes'){
                        showToastr('success', e.message,'');
						window.location.href =e.data.url;
                    }else{
                        showToastr('error', e.message,'');
                    }
                },
                'json'
            );
            App.stopPageLoading();
        }
        kay_value_set = null;
		return false;
    })



</script>