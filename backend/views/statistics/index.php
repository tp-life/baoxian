<?php
$this->title = '数据表格';

?>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-user font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">商户卡券统计列表</span>
                </div>
            </div>
			<div class="note note-danger">
				<p> 统计保险卡券的发放情况 </p>
			</div>
            <div class="portlet-body">

                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm"
                                id="datatable_filter" name="status">
                                <option value="">合作状态</option>
                                <option value="2">合作中</option>
                                <option value="1">已终止</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" size="30" id="datatable_search" name="filter" class="input-md form-filter" placeholder="请输入商户名称/商户ID进行查询"/>
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover"
                           id="datatable_ajax">
                        <thead>

                        <tr role="row" class="heading">
                            <th width="15%"> 商户名称</th>
                            <th width="15%"> 联系人[电话]</th>
                            <th width="10%"> 合作状态</th>
							<th width="6%">激活</th>
							<th width="7%">未激活</th>
							<th width="6%">失效</th>
							<th width="6%">冻结</th>
                            <th width="7%">卡券总计</th>
                            <th width="7%">涉及理赔</th>
                            <th> 操作</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

			<!--统计开始-->
			<div class="overview_card" style="display: none">
				<img  src="<?= Yii::getAlias('@metro') ?>/global/img/loading-spinner-grey.gif" alt="" class="loading col-md-offset-3">
				<span> &nbsp;&nbsp;Loading... </span>
			</div>
			<!--统计结束-->
        </div>
<script  type="text/javascript">
    $(function(){
        EcommerceList.init('<?= \yii\helpers\Url::to(['statistics/sellerlist']) ?>',{},false,'datatable_ajax');
    });

    function handleStatus(seller_id,status){
        $.post('<?=Yii::$app->urlManager->createUrl(['seller/change']);?>',{'_csrf-backend':$('meta[name="csrf-token"]').attr("content"),'seller_id':seller_id,'status':status},function(data){
            if(data.code !='yes'){
                showToastr('error',data.message);
                return false;
            }
            showToastr('success',data.message);
            setTimeout(function(){
                window.location.reload();
            },1000);
        });
    }

</script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		setTimeout('',5000);
		$('.overview_card').fadeIn(2000);
		setTimeout('getOverview()',3000);

	});
	function getOverview() {
		$.getJSON('<?= \yii\helpers\Url::to(['mcoupon/default/overview']) ?>', function (data) {
			$('.overview_card').css('display', 'none');
			if (data.code == 'no') {
				showToastr('warning', data.message);
				return;
			}
			$(".overview_card").replaceWith(data.data.text);

			var chart;
			var legend;
			var chartData  = data.data.data;

			// PIE CHART
			chart = new AmCharts.AmPieChart();
			chart.dataProvider = chartData;
			chart.titleField = "name";
			chart.valueField = "value";
			chart.outlineColor = "#FFFFFF";
			chart.outlineAlpha = 0.8;
			chart.outlineThickness = 2;
			chart.balloonText = "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>";
			// this makes the chart 3D
			chart.depth3D = 15;
			chart.angle = 30;
			// WRITE
			chart.write("chartdiv");


		});
	}





</script>

