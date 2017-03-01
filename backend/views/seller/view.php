
<style>
	.portlet-body h4{ padding-bottom: 5px; margin-top: 25px;color: #222!important;font-weight: bolder;font-size: 20px;}
	.portlet-body hr{ margin-top: 5px}
</style>


<div class="row">
	<div class="col-md-12">
		<!-- Begin: life time stats -->
		<div class="portlet light portlet-fit portlet-datatable bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-settings font-green"></i>
                        <span class="caption-subject font-green sbold uppercase">商家资料#<span class="font-red"><?=$seller['seller_name']?></span>
                        </span>
				</div>
				<div class="actions">

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
										<th>基本资料</th>
									</tr>
									<tr>
										<th class="font-grey-salsa">商家编号</th>
										<td><?= $seller['seller_id'] ?></td>
										<th class="font-grey-salsa">商家名称</th>
										<td><?= $seller['seller_name'] ?></td>
									</tr>
									<tr>

										<th class="font-grey-salsa">会员电话</th>
										<td><?= $user['phone'] ?></td>
										<th class="font-grey-salsa">会员名称</th>
										<td><?= $user['name'] ?></td>
									</tr>
									<tr>

										<th class="font-grey-salsa">联系人</th>
										<td><?=$seller['concat'] ?></td>
										<th class="font-grey-salsa">联系电话</th>
										<td><?=$seller['concat_tel'] ?></td>
									</tr>
									<tr>
										<th class="font-grey-salsa">商户类型</th>
										<td>
											<?php if($seller['is_insurance'] == 1): ?>
												<span class="font-yellow-casablanca">保险商户</span>&nbsp;
											<?php endif; ?>
											<?php if($seller['is_repair'] == 1): ?>
												<span class="font-yellow-casablanca">维修商户</span>
											<?php endif; ?>
										</td>
										<th class="font-grey-salsa">地址详细</th>
										<td><?= $seller['area_info'].' '.$seller['detail_address'] ?></td>
									</tr>
									<tr>

										<th class="font-grey-salsa">商户等级</th>
										<td class="font-yellow-casablanca"><?= !$seller['pid']?'一级商家':'二级商家' ?></td>
										<th class="font-grey-salsa">上级商家</th>
										<td class="font-yellow-casablanca">
											<?php if($seller['pid']): ?>
												<?= $seller['parent_name'] ?>
											<?php endif; ?>
										</td>

									</tr>
									<tr>

										<th class="font-grey-salsa">商户协议</th>
										<td class="font-yellow-casablanca">
											<a class="font-purple-seance" title="点击查看" data-target="#service-responsive" href="<?= \yii\helpers\Url::to(['marticle/default/agreement']) ?>" data-toggle="modal">&lt;&lt;商家合同&gt;&gt;</a>
										</td>
										<th class="font-grey-salsa"></th>
										<td class="font-yellow-casablanca">

										</td>

									</tr>
									<tr>
										<th>银行卡</th>
									</tr>
									<tr>
										<th class="font-grey-salsa">开户银行</th>
										<td><?= $bank['brank_name'] ?></td></tr>
									<tr>
										<th class="font-grey-salsa">开户账户</th>
										<td><?= $bank['account_holder'] ?></td></tr>
									<tr>
										<th class="font-grey-salsa">开户帐号</th>
										<td><?=  $bank['brank_account'] ?></td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>
<!-- begin 协议 -->
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
<!-- end 协议 modal -->


