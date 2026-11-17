<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = '相关链接';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?></p>
<?= GridView::widget(['dataProvider' => $dataProvider, 'columns' => [
    ['attribute' => 'id', 'format' => 'text'],
    ['attribute' => 'label', 'format' => 'text'],
    ['attribute' => 'url', 'format' => 'text'],
    ['attribute' => 'is_public', 'format' => 'text'],
    ['attribute' => 'sort_order', 'format' => 'text'],
    ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {hide}',
        'buttons' => ['hide' => static fn ($url, $record) => Html::a(
            '隐藏',
            ['hide', 'id' => $record->id],
            ['data-method' => 'post', 'data-confirm' => '确认隐藏此记录？']
        )]],
]]) ?>
