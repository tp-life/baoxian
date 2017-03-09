<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商家保险品牌统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@metro'); ?>/global/plugins/zTree/css/metroStyle/metroStyle.css" >
<style>
	ul.ztree {
		margin-top: 10px;
		border: 1px solid #CCCCCC;
		background: #f0f6e4;
		width: 100%;
		height: 360px;
		overflow-y: scroll;
		overflow-x: auto;
	}
</style>
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
			<a attr-url="<?=Yii::$app->urlManager->createUrl('brandst/export')?>"  id="order-download"
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

					<div class="col-sm-3 col-sm-offset-9" style="margin-bottom: 8px">
						<input type="text" id="citySel" class="form-control"   readonly style="width: 75%;display: inline"
							   value="" onclick="showMenu();"
							   placeholder="品牌">
						<input type="hidden" name="brand_id"  id="hiddenSp" class="form-filter">
						<div id="menuContent" class="menuContent" style="display:none; position: absolute; width: 94%; z-index: 99999">
							<ul id="treeDemo" class="ztree" style="margin-top:0; width:100%; height: 300px;"></ul>
						</div>
						<button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
							<i class="fa fa-search"></i> 搜索
						</button>
					</div>

                </div>

            <table class="table table-striped table-bordered table-hover table-checkable dataTable no-footer" id="datatable_list">
                <thead>
                <tr role="row" class="heading">
                    <th width="10%">品牌</th>
                    <th width="10%">型号</th>
                    <th width="7%">激活量</th>
                    <th width="7%">理赔量</th>
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
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/zTree/js/jquery.ztree.core.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/zTree/js/jquery.ztree.excheck.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
		var url = '<?php echo Yii::$app->urlManager->createUrl(['brandst/index'])?>';
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


		var setting = {
			check: {
				enable: true,
				chkStyle: "radio",
				radioType: "all"
			},
			view: {
				dblClickExpand: false
			},
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				onCheck: checkGate,
				onClick: onClick
			}
		};
		<?="var zNodes = ".json_encode($area).';';?>
		//商品分类
		$.fn.zTree.init($("#treeDemo"), setting, zNodes);

	});


	function showData(data) {
		var s_data = data.statistics_text;
		$('#datatable_form .bottom .st').remove();
		$('#datatable_form .bottom').append('<div class="text-right st" style="float: right">'+s_data+'</div>')
	}

	//分类树回调处理选择数据
	function checkGate(event, treeId, treeNode) {
		var name = id = '';
		if (treeNode.checked) {
			name = treeNode.name;
			id = treeNode.id ;
		}
		$('#citySel').val(name);
		$('#hiddenSp').val(id);
	}


	function onClick(e, treeId, treeNode) {
		var zTree = $.fn.zTree.getZTreeObj("treeDemo");
		zTree.checkNode(treeNode, !treeNode.checked, null, true);
		return false;
	}


	function showMenu() {
		var cityObj = $("#citySel");
		var cityOffset = $("#citySel").offset();
		$("#menuContent").slideDown("fast");
		$("body").bind("mousedown", onBodyDown);
	}
	function hideMenu() {
		$("#menuContent").fadeOut("fast");
		$("body").unbind("mousedown", onBodyDown);
	}
	function onBodyDown(event) {
		if (!(event.target.id == "menuBtn" || event.target.id == "citySel" || event.target.id == "menuContent" || $(event.target).parents("#menuContent").length > 0)) {
			hideMenu();
		}
	}
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

