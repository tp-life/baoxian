<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\InsuranceCoverage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Insurance Coverages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="insurance-coverage-view">

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
            'company_id',
            'company_name',
            'type_id',
            'type_name',
            'coverage_name',
            'period',
            'cost_price',
            'official_price',
            'wholesale_price',
            'max_payment',
            'coverage_code',
            'status',
            'is_more',
            'is_delay',
            'image',
            'add_time:datetime',
        ],
    ]) ?>

</div>
