<link href="<?= Yii::getAlias('@metro'); ?>/pages/css/error.min.css" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?= $_SERVER['HTTP_REFERER'] ?>"><?= ucfirst($this->context->id) ?>&nbsp;<?= ucfirst($this->context->action->id) ?></a>
			<i class="fa fa-circle"></i>
		</li>
		<li>
			<span>Error</span>
		</li>
	</ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h3 class="page-title"> <?= $name ?>
	<small></small>
</h3>
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->
<div class="row">
	<div class="col-md-12 page-404">
		<div class="number font-green"> <?= $exception->statusCode?:$exception->getCode() ?> </div>
		<div class="details">
			<h3><?= $name ?></h3>
			<p> <?= nl2br(\yii\helpers\Html::encode($message)) ?></p>
			<p><a class="btn red" href="<?= \yii\helpers\Url::to(['site/index']) ?>"> Return home </a>. </p>
		</div>
	</div>
</div>
