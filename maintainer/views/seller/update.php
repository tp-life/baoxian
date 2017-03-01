<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Seller */

$this->title = 'Update Seller: ' . $model->seller_id;
$this->params['breadcrumbs'][] = ['label' => 'Sellers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->seller_id, 'url' => ['view', 'id' => $model->seller_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="seller-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
