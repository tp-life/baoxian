<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统模块管理';
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
					<th width="10%">编号</th>
					<th width="20%">名称</th>
					<th width="15%">模块名</th>
					<th width="40%">是否有效</th>
					<th width="15%">操作</th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['module/index'])?>';
		EcommerceList.init(url);
	});
</script>