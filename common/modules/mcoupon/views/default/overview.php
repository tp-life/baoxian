<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-social-dribbble font-purple-soft"></i>
			<span class="caption-subject font-purple-soft bold uppercase">卡券总览</span>
		</div>
	</div>
	<div class="portlet-body">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#tab_1_1" data-toggle="tab"> 文本 </a>
			</li>
			<li>
				<a href="#tab_1_2" data-toggle="tab"> 图例 </a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade active in" id="tab_1_1">
				<table class=" table table-bordered  table-hover">
					<thead>
					<tr class="font-grey-salsa">
						<th></th>
						<th width="16%">激活</th>
						<th width="16%">未激活</th>
						<th width="16%">失效</th>
						<th width="16%">冻结</th>
						<th width="16%">总数</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<th class="font-grey-salsa">数量</th>
						<td class="font-yellow-gold font-lg sbold"><?= $num_active ?></td>
						<td class="font-yellow-gold font-lg sbold"><?= $num_default ?></td>
						<td class="font-yellow-gold font-lg sbold"><?= $num_fail ?></td>
						<td class="font-yellow-gold font-lg sbold"><?= $num_froze ?></td>
						<td class="font-yellow-gold font-lg sbold"><?= $num_total ?></td>
					</tr>
					<tr>
						<th class="font-grey-salsa">占比</th>
						<td class="font-yellow-gold font-lg sbold"><?= round($num_active/$num_total,4) *100 ?>%</td>
						<td class="font-yellow-gold font-lg sbold"><?= round($num_default/$num_total,4)*100 ?>%</td>
						<td class="font-yellow-gold font-lg sbold"><?= round($num_fail/$num_total,4)*100 ?>%</td>
						<td class="font-yellow-gold font-lg sbold"><?= round($num_froze/$num_total,4)*100 ?>%</td>
						<td class="font-yellow-gold font-lg sbold"><?= round($num_total/$num_total,4)*100 ?>%</td>
					</tr>
					<?php if($seller): ?>
						<tr>
							<td colspan="6"></td>
						</tr>
						<tr>
							<td class="font-grey-salsa">商家信息</td>
							<td class="font-grey-salsa font-lg sbold" colspan="5">

								<span class="caption-subject">商家名称:<span class="font-purple-medium"><?= $seller->seller_name; ?></span></span>
								<span class="caption-subject">联系人:<span class="font-purple-medium"><?= $seller->concat; ?></span></span>
								<span class="caption-subject">手机号码:<span class="font-purple-medium"><?= $seller->concat_tel; ?></span></span>
								<span class="caption-subject">商家状态:<span class="font-purple-medium"><?= $seller->statusTxt; ?></span></span>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
			<div class="tab-pane fade" id="tab_1_2">
				<div id="chartdiv" style="height: 250px;"></div>
			</div>

		</div>
		<div class="clearfix margin-bottom-20"> </div>
	</div>
</div>
