<?php
$articleFlag = false;
if(Yii::$app->requestedRoute == 'article/create' || Yii::$app->requestedRoute == 'article/update'){
	$articleFlag = true;
}
if ($articleFlag) {
	backend\assets\AppAsset::register($this);
	$this->beginPage();
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="<?= Yii::$app->language ?>" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="<?= Yii::$app->language ?>" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?= Yii::$app->language ?>">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <?php if ($articleFlag) {
        $this->head();
    }  ?>
    <?=\yii\bootstrap\Html::csrfMetaTags()?>
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<meta charset="utf-8" />
	<title>保险后台服务中心 | 乐换新</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta content="保险后台服务中心" name="description" />
	<meta content="保险后台服务中心" name="author" />
	<!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />-->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/fonts/Open_Sans_400_300_600_700_subset_all.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
<!--	<!-- END PAGE LEVEL PLUGINS -->-->
<!--	<!-- BEGIN THEME GLOBAL STYLES -->-->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
<!--	<!-- END THEME GLOBAL STYLES -->-->
<!--	<!-- BEGIN THEME LAYOUT STYLES -->-->
	<link href="<?= Yii::getAlias('@metro'); ?>/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
	<link href="<?= Yii::getAlias('@metro'); ?>/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-toastr/toastr.css" rel="stylesheet" type="text/css" />
	<!-- END THEME LAYOUT STYLES -->

	<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery.min.js" type="text/javascript"></script>
	<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery.storage.js" type="text/javascript"></script>
	<link rel="shortcut icon" href="/favicon.ico" />
	</head>


<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-md">
<?php if ($articleFlag) {
    $this->beginBody();
}  ?>
<!-- BEGIN HEADER -->
<?php require_once __DIR__."/header.php"; ?>
<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"> </div>
<!-- END HEADER & CONTENT DIVIDER -->
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<?php require_once __DIR__."/_left_side_bar.php"; ?>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<!-- BEGIN CONTENT BODY -->
		<div class="page-content">
			<!-- BEGIN PAGE HEADER-->
			<!-- BEGIN THEME PANEL -->

			<?=$content?>
		</div>
		<!-- END CONTENT BODY -->
	</div>
	<!-- END CONTENT -->
	<!-- BEGIN QUICK SIDEBAR -->
	<!-- END QUICK SIDEBAR -->
</div>
<!-- begin images modal -->
<div class="modal fade" id="show_modal" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<div class="modal-header" >
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">图片预览</h4>
			</div>
			<div class="modal-body" id="showimg-width">

			</div>
			<div class="modal-footer">
				<button class="btn btn-sm" onclick="rotateLeft('+')"><i class="fa fa-undo" aria-hidden="true"></i>左旋转</button>
				<button class="btn btn-sm" onclick="rotateLeft('-')"><i class="fa fa-repeat" aria-hidden="true"></i>右旋转</button>
			</div>
		</div>
	</div>
</div>
<!-- end images modal -->
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<?php require_once __DIR__."/footer.php"; ?>
<!-- END FOOTER -->
<?php if ($articleFlag) {
    $this->endBody();
}?>

<!--[if lt IE 9]>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/respond.min.js"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/excanvas.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->

<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
<!--pop tooltip-->
<!--<script src="<?/*= Yii::getAlias('@metro'); */?>/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>-->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>


<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="<?= Yii::getAlias('@metro'); ?>/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>


<!--<script src="<?/*= Yii::getAlias('@metro'); */?>/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/pages/scripts/ui-bootbox.min.js" type="text/javascript"></script>-->
<!--leo datalist core js-->
<script src="<?= Yii::getAlias('@js'); ?>/datalist_of_table_ecommerce.js" type="text/javascript"></script>

<script type="text/javascript">
	var l =0;
	var b =true;
	jQuery(document).ready(function () {
		var w ;
		bootbox.setLocale('zh_CN');
		$(document).on('click','.page-sidebar a,.page-breadcrumb a',function(){
			$.localStorage('filter','');
		});
		$(document).on('click', 'button[name="back"]', function () {
			window.history.go(-1);
		});

		$('.nav-define li.active').each(function(){
			$(this).parents('.nav-define').addClass('active').addClass('open');
		});

		$('body').delegate('.show_img','click',function(){
			l =0;
			b =true;
			$('#show_modal').modal('show');
			$('#showimg-width').html('<img src="" id="modal_img">');
			w = $('.modal-lg').outerWidth() - 30;
			$('#modal_img').attr('src',this.src).css('max-width',w+'px');
		});

		$('#show_modal').on('hidden.bs.modal', function (e) {
			$('#showimg-width').html('');
			$('#showimg-width').css('height','auto');
			$('.modal-lg').width(w+30);
		})

		$('#show_modal').on('shown.bs.modal', function (e) {
			$('.modal-lg').width($('#modal_img').width()+30);
		})

	});

	/**
	 * delete
	 * class bootbox-confirm
	 * attribute rel -> url
	 * attribute data-id->key to delete
	 * */

 	$(document).on('click','.bootbox-confirm', function() {
		var button = $(this);
		var data_id = button.attr('data-id');
		bootbox.confirm("确认删除？#"+data_id, function(result) {
				if(result) {
					var url = button.attr('rel');

					$.getJSON(url,function(backdata){
						//###############DEBUG#######################
						<?php if(YII_DEBUG): ?>
						console.log('url:'+url);
						console.log('response:'+backdata);
						//######################################
						<?php endif; ?>
						if(backdata.code=='yes')
						{
							showToastr('success',backdata.message);
							window.location.reload();
							/*bootbox.alert(backdata.message, function() {
								window.location.href='';
							});*/
						}else{
							showToastr('error',backdata.message?backdata.message:'操作失败');
							/*bootbox.alert(backdata.message?backdata.message:'操作失败');*/
						}
					});
				}
		});
	});

	/**
	 * type success|info|warning|error
	 * title 标题
	 * message Notifications content
	 *
	 * **/
	function showToastr(typeFun,message,title,pos)
	{
		 var attention = {success:"成功提示",info:"温馨提示",warning:"警告提示",error:"错误提示"};

		toastr.options = {
			"closeButton": true,
			"debug": false,
			"positionClass": pos || "toast-top-center",
			"onclick": null,
			"showDuration": "1000",
			"hideDuration": "2000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
		title = title || attention[typeFun];
		toastr[typeFun](message, title);
		/**
		  	 toastr["success"]("success", "Toastr Notifications");
			 toastr.info("info", "Toastr Notifications");
			 toastr.warning("warning", "Toastr Notifications");
			 toastr.error("error", "Toastr Notifications");
		 */
	}
	/**
	 	bootbox
		bootbox.alert('leo',function(){alert('ok')});
		bootbox.confirm('confirm',function(e){alert(e)});
		http://bootboxjs.com/documentation.html
	 */


	/** 火狐下取本地全路径 */
	function getFullPath(obj)
	{
		if(obj)
		{

			// ie
			if (window.navigator.userAgent.indexOf("MSIE")>=1)
			{
				obj.select();
				if(window.navigator.userAgent.indexOf("MSIE") == 25){
					obj.blur();
				}
				return document.selection.createRange().text;
			}
			// firefox
			else if(window.navigator.userAgent.indexOf("Firefox")>=1)
			{
				if(obj.files)
				{
					//return obj.files.item(0).getAsDataURL();
					return window.URL.createObjectURL(obj.files.item(0));
				}
				return obj.value;
			}else if(window.navigator.userAgent.indexOf("Chrome")>=1){
				if(obj.files)
				{
					//return obj.files.item(0).getAsDataURL();
					return window.URL.createObjectURL(obj.files.item(0));
				}
				return obj.value;
			}
			return obj.value;
		}
	}


	function rotateLeft(mod){
		var w = $('#modal_img').width();
		var h = $('#modal_img').height();
		if(mod =='+'){
			l +=90;
		}else if(mod == '-'){
			l -= 90;
		}else{
			return false;
		}
		l = l % 360;
		if(b){
			var _new_w = h;
			var _new_h = w;
			var _d_h = (_new_h - h)/2;
			var _d_w = (_new_w - w)/2;
			$('#showimg-width').height(_new_h);
			$('.modal-lg').width(_new_w + 30);
			$('#modal_img').css('transform','translate('+_d_w+'px,'+_d_h+'px) rotate('+l+'deg)').css('-ms-transform','translate('+_d_w+'px,'+_d_h+'px) rotate('+l+'deg)')
				.css('-webkit-transform','translate('+_d_w+'px,'+_d_h+'px) rotate('+l+'deg)').css('-o-transform','translate('+_d_w+'px,'+_d_h+'px) rotate('+l+'deg)')
				.css('-moz-transform','translate('+_d_w+'px,'+_d_h+'px) rotate('+l+'deg)');
		}else{
			$('#modal_img').css('transform','rotate('+l+'deg)').css('-ms-transform','rotate('+l+'deg)')
				.css('-webkit-transform','rotate('+l+'deg)').css('-o-transform','rotate('+l+'deg)')
				.css('-moz-transform','rotate('+l+'deg)');
			$('#showimg-width').height(h);
			$('.modal-lg').width(w + 30);
		}
		b =!b;
	}
</script>
<!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
<?php $this->endPage() ?>