
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">卡券发放列表</span>
                </div>
                <div class="actions">
					<?= \yii\helpers\Html::a('卡券发放', ['accrod'], ['class' => 'btn blue-hoki']) ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
					<table class="table table-striped table-bordered table-hover table-checkable"
						   id="datatable_ajax">
						<thead>
						<tr role="row" class="heading">

							<th width="10%"> 发放商家</th>
							<th width="15%">公司 类型 名称</th>
							<th width="10%">险种编码</th>
							<th width="8%">数量</th>
							<th width="8%">单价</th>
							<th width="13%">批次货号</th>
<!--							<th width="8%">申领类型</th>-->
<!--							<th width="8%">付款状态</th>-->
							<th width="8%">发放状态</th>
							<th width=""> 操作</th>
						</tr>
						<tr>
							<th></th>
							<th></th>
							<th><input class="form-control form-filter input-sm" name="coverage_code" placeholder="险种编码" type="text"></th>
							<th></th>
							<th></th>
							<th><input class="form-control form-filter input-sm" name="pay_sn" placeholder="批次货号" type="text"></th>
							<?php if(false): ?>
<!--							<th>--><?//= \yii\helpers\Html::dropDownList('apply_type','',\common\models\CardOrderPayback::typeData(),['prompt'=>'请选择','class'=>'form-control form-filter input-sm']) ?><!--</th>-->
<!--							<th>--><?//= \yii\helpers\Html::dropDownList('pay_status','',\common\models\CardOrderPayback::statusData(),['prompt'=>'请选择','class'=>'form-control form-filter input-sm']) ?><!--</th>-->
							<?php endif; ?>
							<th><?= \yii\helpers\Html::dropDownList('status','',\common\models\CardOrderItem::itemStateData(),['prompt'=>'请选择','class'=>'form-control form-filter input-sm']) ?></th>
							<th>
								<button class="btn btn-sm btn-success filter-submit margin-bottom">
									<i class="fa fa-search">搜索</i>
								</button>
								<button class="btn btn-sm red filter-cancel"><i class="fa fa-times">重置</i></button>
							</th>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
                </div>
            </div>

            <div style="display: block">

            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('card/issue')?>', {}, false, 'datatable_ajax');
    });
	$(document).on('click','.apply_cancel', function() {
		var button = $(this);
		bootbox.confirm(button.attr('data-content'), function(result) {
			if(result) {
				var url = button.attr('rel');
				$.getJSON(url,function(backdata){
					//console.log('url:'+url);
					//console.log('response:'+backdata);
					if(backdata.code=='yes')
					{
						showToastr('success',backdata.message);
						window.location.reload();
					}else{
						showToastr('error',backdata.message?backdata.message:'操作失败');
					}
				});
			}
		});
	});

</script>
<!-- begin 协议 -->
<div class="modal fade bs-modal-lg" tabindex="-1" id="my-card-apply" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<img src="<?= Yii::getAlias('@metro') ?>/global/img/loading-spinner-grey.gif" alt="" class="loading">
				<span> &nbsp;&nbsp;Loading... </span>
			</div>
		</div>
	</div>
</div>
<!-- end 协议 modal -->
