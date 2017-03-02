<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '我的关联卡券保险订单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase"> <?= Html::encode($this->title) ?> </span>
		</div>
		<div class="actions">
		</div>
	</div>
	<div class="note note-danger">
<p>我的保险订单</p>
<p>我的卡券关联保险订单</p>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">

					<th width="10%"> 订单编号</th>
					<th width="8%"> 投保用户</th>
					<th width="10%"> 投保手机</th>
					<th width="10%"> IEMI号</th>
					<th width="6%"> 机型</th>
					<th width="8%"> 保险险种</th>
					<th width="8%"> 激活序列号</th>
					<th width="6%"> 订单状态</th>
                    <th width="8%"> 销售商家</th>
					<th width="12%"> 下单时间</th>
					<th width=""> 操作</th>
				</tr>
				<tr>
					<th><input type="text" name="order_sn" class="form-control form-filter input-sm" placeholder="请输入"></th>
					<th><input type="text" name="buyer" class="form-control form-filter input-sm" placeholder="请输入"></th>
					<th><input type="text" name="buyer_phone" class="form-control form-filter input-sm" placeholder="请输入"></th>
					<th><input type="text" name="imei_code" class="form-control form-filter input-sm" placeholder="请输入"></th>
					<th></th>
					<th> <?= \yii\helpers\Html::dropDownList('coverage_code','',$coverage_data,['prompt'=>'选择险种','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?></th>
					<th><input type="text" name="card_number" class="form-control form-filter input-sm" placeholder="请输入"></th>
					<th><?= \yii\helpers\Html::dropDownList('status','',\common\models\Order::getBackendStatusData(),['prompt'=>'状态查询','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?></th>
                    <th> <?= \yii\helpers\Html::dropDownList('seller_id','',$seller_data,['prompt'=>'选择商家','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?></th>
                    <th>
						<div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
							<input class="form-control form-filter input-sm" readonly="" name="add_time_from" placeholder="From" type="text">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-sm default" type="button">
																	<i class="fa fa-calendar"></i>
																</button>
                                                            </span>
						</div>
						<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
							<input class="form-control form-filter input-sm" readonly="" name="add_time_to " placeholder="To" type="text">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-sm default" type="button">
																	<i class="fa fa-calendar"></i>
																</button>
                                                            </span>
						</div>
					</th>
					<th>
						<button class="btn btn-sm btn-success filter-submit margin-bottom">
							<i class="fa fa-search">搜索</i>
						</button>
						<button class="btn btn-sm red filter-cancel"><i class="fa fa-times">重置</i></button>
					</th>
				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

</div>
<script type="text/javascript">
	$(document).ready(function() {
		var url = '<?php echo Yii::$app->urlManager->createUrl(['border/index'])?>';
		var req = {};
		EcommerceList.init(url,req);
	});
</script>