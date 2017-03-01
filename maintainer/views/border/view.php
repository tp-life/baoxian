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
												手机正面
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
												手机背面
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
													<?= $order['server_mark'] ?>
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
