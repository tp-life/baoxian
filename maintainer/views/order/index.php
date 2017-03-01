<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '维保订单';
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
<p>.系统指派商家理赔订单</p>
<p>.商家根据理赔情况更新实际进度</p>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="10%">ID</th>
					<th width="10%">保单号</th>
					<th width="8%">理赔类型</th>
					<th>联系人</th>
					<th>联系电话</th>
					<th width="10%">状态</th>
					<th width="15%">指派时间</th>
					<th width="20%">操作处理</th>
				</tr>
				<tr>
					<th><input type="text" name="id" class="form-control form-filter input-sm" placeholder="理赔编号"></th>
					<th><input type="text" name="order_sn" class="form-control form-filter input-sm" placeholder="订单编号"></th>
					<th><?= Html::dropDownList('type','',\common\models\OrderMaintenance::typeData(),['prompt'=>'理赔类型','class'=>'form-control form-filter input-sm']) ?></th>
					<th><input type="text" name="contact" class="form-control form-filter input-sm" placeholder="联系人"></th>
					<th><input type="text" name="contact_number" class="form-control form-filter input-sm" placeholder="联系电话"></th>

					<th>
						<?= Html::dropDownList('service_status','',\common\models\OrderMaintenanceService::serviceStateData(),['prompt'=>'商家状态','class'=>'form-control form-filter input-sm']) ?>
					</th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['order/index'])?>';
		var req = {};
		EcommerceList.init(url,req);
	});
</script>