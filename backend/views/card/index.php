<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡券列表';
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
			<?= Html::a('卡券生成', ['create'], ['class' => 'btn blue-hoki']) ?>
			<a attr-url="<?=Yii::$app->urlManager->createUrl('card/export')?>"  id="order-download"
			   class="btn  blue  btn-sm ">
				<i class="fa fa-download"></i> 导出
			</a>
		</div>
	</div>
	<div class="col-md-12">
		<div class="note note-danger">
			<p> 卡券导出默认导出当前条件下全部卡券,<font color="#ff4500">最好不要超过5000张</font></p>
			<p> <font color="#ff4500">通过加强筛选条件导出，导出数据限制5000张</font></p>
			<p> 卡券导出可通过复选框指定导出</p>
		</div>
	</div>
    <div class="portlet-body">
        <div class="table-container">
            <form id="datatable_form">
                <div class="table-search text-right">
					<?= Html::dropDownList('seller_id','',$list_seller,['prompt'=>'按商家查询','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
					&nbsp;&nbsp;
					<?= Html::dropDownList('status','',\common\models\CardCouponsGrant::statusData(),['prompt'=>'按状态查询','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
                    &nbsp;&nbsp;
                    <select
                        class="table-group-action-input form-control form-filter input-inline  input-sm"
                        id="datatable_filter" name="search_type">
                        <option value="">搜索类型</option>
                        <option value="1">卡券序号</option>
                        <option value="2">险种</option>
                    </select>
                    <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入卡券序号/险种进行查询"/>
                    <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                </div>

            <table class="table table-striped table-bordered table-hover table-checkable dataTable no-footer" id="datatable_list">
                <thead>
                <tr role="row" class="heading">
                   <th width="2%"><input type="checkbox"  name="id_all" class="group-checkable"></th>
                    <th width="5%">ID # </th>
                    <th width="10%">卡券序列号</th>
                    <th width="10%">卡券密匙</th>
                    <th width="10%">生成时间</th>
                    <th width="20%">险种</th>
                    <th width="5%">卡券状态</th>
                    <th width="10%">所属商家</th>
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
        var url = '<?php echo Yii::$app->urlManager->createUrl(['card/index'])?>';
        var req = {};
        EcommerceList.init(url,req);

		//条件导出下载
        $('#order-download').on('click',function(){
            var parm=$('#datatable_form').serialize();
			var href = $(this).attr('attr-url');
			href +='?'+parm;
			console.log(href);
			bootbox.confirm("确认导出当前条件选中或者当前条件下所有卡券?&nbsp;<font color='#ff4500'>建议导出总量不要超过5000张</font>", function(result) {
				if(result){
					window.open(href, "_blank")
				}
			});
        });
    });
</script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		setTimeout('',2000);
		$('.overview_card').fadeIn(1000);
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

