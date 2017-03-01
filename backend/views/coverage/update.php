<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\InsuranceCoverage */

$this->title = 'Update Insurance Coverage: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Insurance Coverages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="insurance-coverage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
