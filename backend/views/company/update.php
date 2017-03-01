<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\InsuranceCompany */

$this->title = 'Update Insurance Company: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Insurance Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="insurance-company-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
