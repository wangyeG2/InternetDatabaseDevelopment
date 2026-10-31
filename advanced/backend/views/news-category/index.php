<?php

/** Category management list. Variable: dataProvider. Maintainer: B. */
declare(strict_types=1);

use backend\assets\NewsAsset;
use yii\grid\GridView;
use yii\helpers\Html;

NewsAsset::register($this);
$this->title = '新闻分类管理';
$this->params['breadcrumbs'][] = ['label' => '新闻管理', 'url' => ['/news/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="news-admin">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('新建分类', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('返回新闻管理', ['/news/index'], ['class' => 'btn btn-outline-secondary']) ?></p>
    <p>停用分类后，其新闻会在前台隐藏，后台仍可编辑。分类保留以维护已有新闻的关联。</p>
    <div class="table-responsive"><?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['attribute' => 'name', 'label' => '分类名称'],
            ['attribute' => 'slug', 'label' => '标识'],
            ['attribute' => 'description', 'label' => '说明'],
            ['attribute' => 'sort_order', 'label' => '顺序'],
            ['attribute' => 'is_active', 'label' => '启用', 'format' => 'boolean'],
            ['class' => yii\grid\ActionColumn::class, 'template' => '{update}'],
        ],
    ]) ?></div>
</section>
