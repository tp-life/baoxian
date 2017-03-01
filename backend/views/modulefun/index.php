<?php
use yii\helpers\Html;
?>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?= \yii\helpers\Url::to(['module/index'])?>"><?=$module->name ?></a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<span>方法列表</span>
		</li>
	</ul>
</div>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase">方法列表 </span>
		</div>
		<div class="actions">
			<button class="btn  red" name="back" type="button"><i class="fa fa-angle-left"></i> 返回</button>
			<?= Html::a('添 加', \yii\helpers\Url::to(['modulefun/create','module_id'=>$module->id]), ['class' => 'btn blue-hoki']) ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="10%">编号</th>
					<th width="20%">模块方法名称</th>
					<th width="15%">模块方法</th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['modulefun/index'])?>';
		var rq = {module_id:<?=$module->id?>};
		EcommerceList.init(url,rq);
	});
</script>