<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡券退回';
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
<p>卡券退回处理</p>
<p>商家状态卡券退回管理</p>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="15%">退回编号</th>
					<th width="10%">退回商户</th>
					<th width="10%">退回数量</th>
					<th width="15%">总计金额</th>
					<th width="15%">处理状态</th>
					<th width="20%">申请时间</th>
					<th width="">操作处理</th>
				</tr>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th>
						<?= Html::dropDownList('status','',\common\models\CardRefund::refundStateData(),['prompt'=>'请选择退回状态','class'=>'form-control form-filter input-sm']) ?>
					</th>
					<th></th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['cardrefund/index'])?>';
		var req = {};
		EcommerceList.init(url,req);
	});
</script>