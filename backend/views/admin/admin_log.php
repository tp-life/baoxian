<?php
use yii\helpers\Html;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase">管理员日志列表</span>
		</div>
		<div class="actions">
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="5%">编号</th>
					<th width="10%">管理员</th>
					<th>描述</th>
					<th width="10%">时间</th>
					<th width="15%">操作</th>
				</tr>
				<tr>
					<th></th>
					<th>
						<input type="text" name="username" class="form-control form-filter input-sm" placeholder="请输入名字">
					</th>
					<th>
					</th>
					<th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['admin/log','id'=>intval($_REQUEST['id'])])?>';
		EcommerceList.init(url);
	});
</script>