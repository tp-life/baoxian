<?php
use yii\helpers\Html;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase">文章分类列表</span>
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
					<th width="10%">分类编号</th>
					<th width="15%">分类名称</th>
					<th width="15%">分类简介</th>
<!--					<th width="15%">文章类型</th>-->
					<th width="15%">是否有效</th>
					<th width="10%">分类排序</th>
					<th width="">分类操作</th>
				</tr>
				<tr>
					<th></th>
					<th>
						<input type="text" name="title" class="form-control form-filter input-sm" placeholder="分类名称">
					</th>
					<th>
					</th>
<!--					<th>-->
<!--						--><?php //echo Html::dropDownList('pid','',$categoryType,['prompt'=>'选择类型','class'=>'form-control form-filter']) ?>
<!---->
<!--					</th>-->
					<th>
						<select name="is_effect" class="form-control form-filter">
							<option value="">是否</option>
							<option value="1">是</option>
							<option value="0">否</option>
						</select>
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['articlecategory/index'])?>';
		EcommerceList.init(url);
	});
</script>