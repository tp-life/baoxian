<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统配置';
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
					<th width="10%">配置键</th>
					<th width="20%">配置值</th>
					<th width="15%">类别</th>
					<th width="40%">中文描述</th>
					<th width="15%">操作</th>
				</tr>
				<tr>
					<th><input type="text" name="name" class="form-control form-filter input-sm" placeholder="键名"></th>
					<th>
						<input type="text" name="value" class="form-control form-filter input-sm" placeholder="键值">
					</th>
					<th>
						<?php echo Html::dropDownList('pid','',$confType,['prompt'=>'选择类型','class'=>'form-control form-filter']) ?>
					</th>
					<th></th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['conf/index'])?>';
		EcommerceList.init(url);
	});
</script>