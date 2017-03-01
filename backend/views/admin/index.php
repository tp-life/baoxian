<?php
use yii\helpers\Html;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase">管理员列表</span>
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
					<th width="10%">名称 </th>
					<th width="15%">电话 </th>
					<th width="10%">系统管理</th>
					<th width="10%">角色</th>
					<th width="15%">登录时间</th>
					<th width="10%">登录IP</th>
					<th width="">操作</th>
				</tr>
				<tr>
					<th><input type="text" name="id" class="form-control form-filter input-sm" placeholder="编号"></th>
					<th>
						<input type="text" name="username" class="form-control form-filter input-sm" placeholder="名字">
					</th>
					<th>
						<input type="text" name="phone" class="form-control form-filter input-sm" placeholder="电话">
					</th>
					<th>
						<select name="is_system" class="form-control form-filter">
							<option value="">是否</option>
							<option value="1">是</option>
							<option value="0">否</option>
						</select>
					</th>
					<th>
						<input type="text" name="role_name" class="form-control form-filter input-sm" placeholder="角色名">
					</th>
					<th>

						<div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
							<input class="form-control form-filter input-sm" readonly="" name="login_at_from" placeholder="From" type="text">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-sm default" type="button">
																	<i class="fa fa-calendar"></i>
																</button>
                                                            </span>
						</div>
						<div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
							<input class="form-control form-filter input-sm" readonly="" name="login_at_to " placeholder="To" type="text">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-sm default" type="button">
																	<i class="fa fa-calendar"></i>
																</button>
                                                            </span>
						</div>

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
		var url = '<?php echo Yii::$app->urlManager->createUrl(['admin/index'])?>';
		EcommerceList.init(url);
	});
</script>