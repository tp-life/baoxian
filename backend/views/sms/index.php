<?php
use yii\helpers\Html;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase">短信发送日志列表</span>
		</div>
		<div class="actions">
			<?= Html::a('发短信', ['create'], ['class' => 'btn blue-hoki']) ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover" id="datatable_list">
				<thead>
				<tr role="row" class="heading">
					<th width="12%">类型</th>
					<th width="12%">电话</th>
					<th>内容</th>
					<th width="10%">发送时间</th>
					<th width="15%">操作</th>
				</tr>
				<tr>

					<th>
						<?= Html::dropDownList('type','',\common\tool\Sms::getTypeData(),['prompt'=>'选择短信类型','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
					</th>
					<th>
						<input type="text" name="phone" class="form-control form-filter input-sm" placeholder="请输入电话号码">
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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['sms/index'])?>';
		EcommerceList.init(url);
	});
</script>