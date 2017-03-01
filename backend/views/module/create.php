<div class="row">
	<div class="col-md-12">
		<!-- BEGIN VALIDATION STATES-->
		<div class="portlet light portlet-fit portlet-form bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="icon-settings font-red"></i>
					<span class="caption-subject font-red sbold uppercase">模块添加</span>
				</div>
				<div class="actions">
					<div class="btn-group btn-group-devided" data-toggle="buttons">
						<button class="btn btn-transparent red btn-outline btn-circle btn-sm active" name="back" type="button"><i class="fa fa-angle-left"></i> 返回</button>

					</div>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->render('_form', [
					'model' => $model,
				]) ?>
			</div>
		</div>
		<!-- END VALIDATION STATES-->
	</div>
</div>
