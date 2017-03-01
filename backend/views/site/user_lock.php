<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.5
Version: 4.5.2
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<title>Metronic | User Lock Screen 1</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />-->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/fonts/Open_Sans_400_300_600_700_subset_all.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN THEME GLOBAL STYLES -->
	<link href="<?= Yii::getAlias('@metro'); ?>/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="<?= Yii::getAlias('@metro'); ?>/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
	<!-- END THEME GLOBAL STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link href="<?= Yii::getAlias('@metro'); ?>/pages/css/lock.min.css" rel="stylesheet" type="text/css" />
	<!-- END PAGE LEVEL STYLES -->
	<!-- BEGIN THEME LAYOUT STYLES -->
	<!-- END THEME LAYOUT STYLES -->
	<link rel="shortcut icon" href="favicon.ico" /> </head>
<!-- END HEAD -->

<body class="">
<div class="page-lock">
	<div class="page-logo">
		<a class="brand" href="index.html">
			<img src="<?= Yii::getAlias('@metro'); ?>/pages/img/logo-big.png" alt="logo" /> </a>
	</div>
	<div class="page-body">
		<div class="lock-head"> 锁屏 </div>
		<div class="lock-body">
			<div class="pull-left lock-avatar-block">
				<img src="<?= Yii::getAlias('@metro'); ?>/pages/media/profile/photo3.jpg" class="lock-avatar"> </div>
			<form class="lock-form pull-left" action="<?= Yii::$app->urlManager->createUrl('site/login') ?>" method="post">
				<h4><?= Yii::$app->user->identity->username ?></h4>
				<div class="form-group">
					<input type="hidden" name="_csrf-backend" value="<?=Yii::$app->request->csrfToken?>">
					<input type="hidden" name="LoginForm[username]" value="<?= Yii::$app->user->identity->username ?>">
					<input type="hidden" name="LoginForm[remember]" value="1" />
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" name="LoginForm[password]" /> </div>
				<div class="form-actions">
					<button type="submit" class="btn red uppercase">登录</button>
				</div>
			</form>
		</div>
		<div class="lock-bottom">
			<a href="<?= Yii::$app->urlManager->createUrl('site/login') ?>">不是 <?= Yii::$app->user->identity->username ?>?</a>
			<?php Yii::$app->user->logout(); ?>
		</div>
	</div>
	<div class="page-footer-custom"> 2016 © 乐换新. Admin For Insurance. </div>
</div>
<!--[if lt IE 9]>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/respond.min.js"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/excanvas.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= Yii::getAlias('@metro'); ?>/pages/scripts/lock.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
</body>

</html>