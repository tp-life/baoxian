<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CardCouponsGrant */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Card Coupons Grants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-coupons-grant-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'card_number',
            'card_secret',
            'seller_id',
            'status',
            'active_time',
            'ymd',
            'order_id',
            'coverage_id',
            'coverage_code',
            'type_id',
            'company_id',
            'created',
        ],
    ]) ?>

</div>
