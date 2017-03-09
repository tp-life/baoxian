<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商家保险销售整体统计';
$this->params['breadcrumbs'][] = $this->title;
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
			<i class="icon-settings font-green"></i>
			<span class="caption-subject font-green sbold uppercase"> <?= Html::encode($this->title) ?> </span>
		</div>
		<div class="actions">
			<a attr-url="<?=Yii::$app->urlManager->createUrl('overall/export')?>"  id="order-download"
			   class="btn  blue  btn-sm ">
				<i class="fa fa-download"></i> 导出
			</a>
		</div>
	</div>
	<div class="col-md-12">

	</div>
    <div class="portlet-body">
        <div class="table-container">
            <form id="datatable_form">
                <div class="table-search text-right">
					<?php if(!$level): ?>
					<select
						class="table-group-action-input form-control form-filter input-inline  input-sm"
						 name="diff">
						<option value="">库存状态</option>
						<option value="1">充足</option>
						<option value="2">不足</option>
					</select>
					&nbsp;&nbsp;&nbsp;
					<select
						class="table-group-action-input form-control form-filter input-inline  input-sm"
						id="datatable_filter" name="level">
						<option value="">商家等级</option>
						<option value="1">自营</option>
						<option value="2">二级商家</option>
					</select>
					&nbsp;&nbsp;&nbsp;

                    <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入商家名称/联系人/联系电话进行查询"/>
                    <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                        <i class="fa fa-search"></i> 搜索
                    </button>
					<?php endif; ?>
                </div>

            <table class="table table-striped table-bordered table-hover table-checkable dataTable no-footer" id="datatable_list">
                <thead>
                <tr role="row" class="heading">
                    <th width="14%">商家名称</th>
                    <th width="15%">联系人【电话】</th>
                    <th width="7%">商家等级</th>
					<th width="7%">卡券总数</th>
                    <th width="6%">已激活</th>
                    <th width="6%">未激活</th>
                    <th width="6%">库存状态</th>
                    <th width="6%">失效</th>
                    <th width="6%">冻结</th>
                    <th width="6%">理赔</th>
                    <th width="6%">激活率</th>
                    <th width="6%">理赔率</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
			</form>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        var url = '<?php echo Yii::$app->urlManager->createUrl(['overall/index'])?>';
        var req = {};
        EcommerceList.init(url,req,false,'','',showData);

		//条件导出下载
        $('#order-download').on('click',function(){
            var parm=$('#datatable_form').serialize();
			var href = $(this).attr('attr-url');
			href +='?'+parm;
			bootbox.confirm("确认导出当前条件下所有统计数据?&nbsp;", function(result) {
				if(result){
					window.open(href, "_blank")
				}
			});
        });
    });
	
	function showData(data) {
		var s_data = data.statistics_text;
		$('#datatable_form .bottom .st').remove();
		$('#datatable_form .bottom').append('<p class="text-right st" style="float: right">'+s_data+'</p>')
	}
</script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){


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

