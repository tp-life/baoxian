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
					<span class="caption-subject font-green sbold uppercase">激活详细</span>
				</div>
			</div>
			<div class="note note-danger">
				<p> 统计保险卡券激活详情 </p>
			</div>

            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                    <span> </span>
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <label class="checkbox-inline">
                                <input type="radio" name="datetime" class="form-filter" value="0" checked> 最近7天
                            </label>
                            <label class="checkbox-inline">
                                <input type="radio" name="datetime" class="form-filter" value="1"> 本月
                            </label>
                            <label class="checkbox-inline">
                                <input type="radio" name="datetime" class="form-filter" value="2"> 最近30天
                            </label>

                            <label class="checkbox-inline">
                                <input type="radio" name="datetime" class="form-filter" value="3"> 自定义
                                <input type="text" size="30" id="datatable_search" name="s_time" class="input-md date date-picker form-filter" data-date-format="yyyy-mm-dd" placeholder="From"/>
                                <i class="fa fa-calendar"></i>至
                                <input type="text" size="30" id="datatable_search" name="e_time" class="input-md date date-picker form-filter" data-date-format="yyyy-mm-dd" placeholder="To"/>
                                <i class="fa fa-calendar"></i>
                            </label>

                        </span>
                            <input type="hidden" name="seller_id" value="<?= $seller_id; ?>" />
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>

                        <tr role="row" class="heading">
<!--                            <th width="2%"><input type="checkbox" class="group-checkable"></th>-->
                            <th width="5%">ID # </th>
                            <th width="10%"> 激活日期</th>
                            <th width="10%"> 激活序号</th>
                            <th width="8%"> 购买人</th>
                            <th width="8%"> 联系电话</th>
                            <th width="8%"> 险种</th>
                            <th width="8%"> 手机品牌</th>
                            <th width="8%"> IEMI号</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
		</div>

<script  type="text/javascript">
    $(function(){
        EcommerceList.init('/statistics/activatelist',{'seller_id':<?= $seller_id; ?>},false,'datatable_ajax');
    });
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


