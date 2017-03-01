<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title">申领卡券批次详情</h4>
</div>
<div class="modal-body">

		<table  class="table table-bordered table-hover">
			<thead>

			</thead>
			<tbody>
			<tr>
				<th>批次信息</th>
			</tr>
			<tr>
				<th class="font-grey-salsa">批次编号</th>
				<td class="sbold"><?= $model->pay_sn; ?></td>

				<th class="font-grey-salsa">付款状态</th>
				<td  class="font-purple-seance sbold"><?= \common\models\CardOrderPayback::getMsg($model->pay_status); ?></td>

			</tr>
			<tr>
				<th class="font-grey-salsa">申领类型</th>
				<td class=""><?= \common\models\CardOrderPayback::getTypeMsg($model->apply_type); ?></td>
				<th class="font-grey-salsa"></th>
				<td  class="font-purple-seance "></td>

			</tr>
			<tr>
				<th class="font-grey-salsa">申领总量</th>
				<td class=""><?= $model->num; ?></td>
				<th class="font-grey-salsa">申领总价</th>
				<td  class="font-purple-seance"><?= $model->total_price; ?></td>
				<th class="font-grey-salsa">已支付款</th>
				<td  class="font-purple-seance"><?= $model->received_price; ?></td>

			</tr>

			<tr>
				<th>卡券订单</th>
			</tr>
			<tr>
				<th class="font-grey-salsa">订单编号</th>
				<th class="font-grey-salsa" width="10%">公司 类型 名称</th>
				<th class="font-grey-salsa">险种编码</th>
				<th class="font-grey-salsa">数量</th>
				<th class="font-grey-salsa">单价</th>
				<th class="font-grey-salsa">状态</th>
                <th class="font-grey-salsa">发放时间</th>
			</tr>
			<?php if($orders = $model->getOrders()): ?>
				<?php foreach($orders as $order): ?>
			<tr>
				<td><?= $order->order_id ?></td>
				<td ><?php  if($_info = $order->getCoverageInfo()){
						echo $_info['company_name'].' '.$_info['type_name'].' '.$_info['coverage_name'];
					} ?></td>
				<td ><?= $order->coverage_code ?></td>
				<td class="font-yellow-casablanca" ><?= $order->number ?></td>
				<td class="font-yellow-casablanca"><?= $order->price ?></td>
				<td class="font-yellow-casablanca"><?= $order->getStatusText() ?></td>
                <td class="font-yellow-casablanca"><?php echo  $order->send_time ? date("Y-m-d H:i",$order->send_time) : '' ?></td>
			</tr>
			<tr class="font-green-sharp">
				<th>操作信息</th>
			</tr>
					<tr>
						<th class="font-grey-salsa">操作者</th>
						<th class="font-grey-salsa">操作日志</th>
						<th class="font-grey-salsa">操作时间</th>
					</tr>
					<?php if($log = $order->getLogInfo()): ?>
						<?php foreach($log as $row): ?>
							<tr class="font-green-sharp">

								<td><?= $row['name'] ?></td>
								<td><?= $row['content'] ?></td>
								<td><?= $row['update_time'] ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					<tr><td colspan="3"><hr></td></tr>
			<?php endforeach; ?>
			<?php endif; ?>


			</tbody>
		</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn default" data-dismiss="modal">关闭</button>
	<!--<button type="button" class="btn blue">Save changes</button>-->
</div>
