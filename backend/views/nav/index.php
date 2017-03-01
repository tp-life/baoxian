<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '子菜单管理';
?>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?= \yii\helpers\Url::to(['group/index']) ?>"><?= $navGroup->name?></a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<span>子菜单</span>
		</li>
	</ul>

</div>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase"> <?= Html::encode($this->title) ?> </span>
		</div>
		<div class="actions">
			<button class="btn  red" name="back" type="button"><i class="fa fa-angle-left"></i> 返回</button>
			<?= Html::a('添 加', ['nav/create','group_id'=>$navGroup->id], ['class' => 'btn blue-hoki']) ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="10%">名称</th>
					<th width="20%">图标</th>
					<th width="15%">是否有效</th>
					<th width="40%">排序</th>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['nav/index'])?>';
		var rq = {
			group_id:'<?= $navGroup->id ?>'
		};
		EcommerceList.init(url,rq);
	});
</script>