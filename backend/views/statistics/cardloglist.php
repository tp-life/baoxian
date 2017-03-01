<?php
$this->title = '数据表格';

?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<!--统计开始-->
	<div class="overview_card" style="display: none">
		<img  src="<?= Yii::getAlias('@metro') ?>/global/img/loading-spinner-grey.gif" alt="" class="loading col-md-offset-3">
		<span> &nbsp;&nbsp;Loading... </span>
	</div>
	<!--统计结束-->
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-user font-green"></i>
			<span class="caption-subject font-green sbold uppercase">商家日志</span>
		</div>
	</div>
	<div class="note note-danger">
		<p> 统计保险卡券的发放情况 </p>
	</div>

	<div class="portlet-body">
		<div class="table-actions-wrapper ">
			<span> </span>


		</div>
		<div class="table-container">
			<table class="table table-striped table-bordered table-hover table-checkable"
				   id="datatable_ajax">
				<thead>

				<tr role="row" class="heading">
					<th width="5%">ID # </th>
					<th width="7%"> 日志类型</th>
					<th width="21%"> 描述</th>
					<th width="8%"> 日志时间</th>
				</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<script  type="text/javascript">
    $(function(){
        EcommerceList.init('/statistics/cardloglist',{'seller_id':<?= $seller_id; ?>},false,'datatable_ajax');
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
		$.getJSON('<?= \yii\helpers\Url::to(['mcoupon/default/overviewseller','seller_id'=>$seller_id]) ?>', function (data) {
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

