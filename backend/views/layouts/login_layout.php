<!DOCTYPE html>

<!--[if IE 8]> <html lang="<?= Yii::$app->language ?>" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="<?= Yii::$app->language ?>" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?= Yii::$app->language ?>">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="<?= Yii::$app->charset ?>" />
	<title><?= $this->context->title ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta content="<?= $this->context->description ?>" name="description" />
	<meta content="<?= $this->context->author ?>" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />-->
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/fonts/Open_Sans_400_300_600_700_subset_all.css" rel="stylesheet" type="text/css" />-->
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />-->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />-->
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />-->
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />-->
<!--	<link href="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />-->
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN THEME GLOBAL STYLES -->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
	<!-- END THEME GLOBAL STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link href="<?= Yii::getAlias('@metro'); ?>/pages/css/login.min.css" rel="stylesheet" type="text/css" />
	<!-- END PAGE LEVEL STYLES -->
	<!-- BEGIN THEME LAYOUT STYLES -->
	<!-- END THEME LAYOUT STYLES -->
	<link rel="shortcut icon" href="favicon.ico" /> </head>
<!-- END HEAD -->

<body class="login">
<div class="menu-toggler sidebar-toggler"></div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="javascript:;">
		<img src="<?= Yii::getAlias('@metro'); ?>/pages/img/logo-big.png" title="登录" alt="" /> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
	<?= $content ?>
</div>
<div class="copyright"> 2016 © 乐换新. Admin For Insurance. </div>
<!--[if lt IE 9]>
<!--<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/respond.min.js"></script>-->
<!--<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/excanvas.min.js"></script>-->
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/jquery.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/js.cookie.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>-->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>-->
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!--<script src="<?/*= Yii::getAlias('@metro'); */?>/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?/*= Yii::getAlias('@metro'); */?>/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="<?/*= Yii::getAlias('@metro'); */?>/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>-->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/global/scripts/app.min.js" type="text/javascript"></script>-->
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<!--<script src="--><?//= Yii::getAlias('@metro'); ?><!--/pages/scripts/login.min.js" type="text/javascript"></script>-->
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>