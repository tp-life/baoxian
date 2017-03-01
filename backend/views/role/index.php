<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase"> <?= Html::encode($this->title) ?> </span>
		</div>
		<div class="actions">
			<?= Html::a('添 加', ['create'], ['class' => 'btn blue-hoki']) ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="10%">编号 </th>
					<th width="40%">名称 </th>
					<th width="30%">系统角色</th>
					<th width="20%">操作</th>
				</tr>
				<tr>
					<th><input type="text" name="id" class="form-control form-filter input-sm" placeholder="编号"></th>
					<th>
						<input type="text" name="name" class="form-control form-filter input-sm" placeholder="角色名称">
					</th>
					<th>
						<select name="isSystem" class="form-control form-filter">
							<option value="">是否系统角色</option>
							<option value="1">是</option>
							<option value="0">否</option>
						</select>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['role/index'])?>';
		var req = {};
		EcommerceList.init(url,req);
	});
</script>