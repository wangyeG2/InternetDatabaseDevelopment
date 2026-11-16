<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = '团队资料';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::a('编辑当前资料', ['update'], ['class' => 'btn btn-success']) ?></p>
<?= GridView::widget(['dataProvider' => $dataProvider, 'columns' => [
    ['attribute' => 'id', 'format' => 'text'],
    ['attribute' => 'name', 'format' => 'text'],
    ['attribute' => 'slug', 'format' => 'text'],
]]) ?>
