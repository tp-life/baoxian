<?php
use yii\helpers\Html;
?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase">文章列表</span>
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
					<th width="15%">标题 </th>
					<th width="15%">所在分类 </th>
					<th width="10%">作者 </th>
					<th width="10%">状态 </th>
					<th width="10%">排序</th>
					<th width="15%">添加时间</th>
					<th width="">操作</th>
				</tr>
				<tr>
					<th><input type="text" name="id" class="form-control form-filter input-sm" placeholder="编号"></th>
					<th>
						<input type="text" name="title" class="form-control form-filter input-sm" placeholder="标题">
					</th>
					<th>
                        <select id="pid"  name="pid" class="form-control form-filter  input-inline">
                            <option value="">全部</option>
							<option value="0">保险系列</option>
                            <?php foreach ($ArticleCategoryList as $item) {?>
                                <option value="<?=$item['id']?>"><?=str_repeat('&emsp;', ($item['deep']-1)*2)?><?=$item['title']?></option>
                            <?php }?>
                        </select>
					</th>
                    <th>
                        <input type="text" name="author" class="form-control form-filter input-sm" placeholder="作者">
                    </th>
					<th>
						<select name="status" class="form-control form-filter">
							<option value="">全部</option>
							<option value="1">启用</option>
							<option value="0">禁用</option>
						</select>
					</th>
                    <th>

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
		EcommerceList.init('<?=$url?>');
	});
</script>