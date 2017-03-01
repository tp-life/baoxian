
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">卡券申请列表</span>
                </div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search">
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <div class="btn-group " >
                                        <a href="<?=Yii::$app->urlManager->createUrl('card/applf')?>" class="btn red-sunglo" >
                                            <i class="fa fa-bell"></i>  卡券申请</a>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">

                            <th width="5%"> ID&nbsp;#</th>
                            <th width="15%">公司 类型 名称</th>
                            <th width="10%">险种编码</th>
                            <th width="5%">数量</th>
                            <th width="5%">单价</th>
							<th width="13%">批次货号</th>
							<th width="">申领类型</th>
							<th width="">付款状态</th>
                            <th width="">发放状态</th>
                            <th width=""> 操作</th>
                        </tr>
						<tr>
							<th></th>
							<th></th>
							<th><input class="form-control form-filter input-sm" name="coverage_code" placeholder="险种编码" type="text"></th>
							<th></th>
							<th></th>
							<th><input class="form-control form-filter input-sm" name="pay_sn" placeholder="批次货号" type="text"></th>
							<th><?= \yii\helpers\Html::dropDownList('apply_type','',\common\models\CardOrderPayback::typeData(),['prompt'=>'请选择申领类型','class'=>'form-control form-filter input-sm']) ?></th>
							<th><?= \yii\helpers\Html::dropDownList('pay_status','',\common\models\CardOrderPayback::statusData(),['prompt'=>'请选择付款状态','class'=>'form-control form-filter input-sm']) ?></th>
							<th><?= \yii\helpers\Html::dropDownList('status','',\common\models\CardOrderItem::itemStateData(),['prompt'=>'请选择发放状态','class'=>'form-control form-filter input-sm']) ?></th>
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
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?= \yii\helpers\Url::to(['card/index'])?>', {}, false, 'datatable_ajax');
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
