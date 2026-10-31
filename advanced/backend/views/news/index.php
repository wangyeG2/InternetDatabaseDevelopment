<?php

/** Management list view. Variables: search, dataProvider, categories. Maintainer: B. */
declare(strict_types=1);

use backend\assets\NewsAsset;
use common\models\News;
use yii\grid\GridView;
use yii\helpers\Html;

NewsAsset::register($this);
$this->title = '新闻公告管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="news-admin">
    <div class="news-admin-heading"><h1><?= Html::encode($this->title) ?></h1>
        <div><?= Html::a('新建新闻', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('分类管理', ['/news-category/index'], ['class' => 'btn btn-outline-secondary']) ?></div></div>
    <form action="<?= Html::encode(yii\helpers\Url::to(['index'])) ?>" method="get" class="row g-3 mb-4">
        <?= Html::hiddenInput('r', 'news/index') ?>
        <div class="col-md-5"><?= Html::label('关键词', 'admin-news-keyword', ['class' => 'form-label']) ?>
            <?= Html::textInput('q', $search->q, ['id' => 'admin-news-keyword', 'class' => 'form-control']) ?></div>
        <div class="col-md-3"><?= Html::label('分类', 'admin-news-category', ['class' => 'form-label']) ?>
            <?= Html::dropDownList('category', $search->category, $categories, ['id' => 'admin-news-category', 'class' => 'form-select', 'prompt' => '全部']) ?></div>
        <div class="col-md-2"><?= Html::label('状态', 'admin-news-status', ['class' => 'form-label']) ?>
            <?= Html::dropDownList('status', $search->status, News::statusOptions(), ['id' => 'admin-news-status', 'class' => 'form-select', 'prompt' => '全部']) ?></div>
        <div class="col-md-2 d-flex align-items-end"><?= Html::submitButton('筛选', ['class' => 'btn btn-primary w-100']) ?></div>
    </form>
    <?= Html::errorSummary($search, ['class' => 'alert alert-danger']) ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
            'emptyText' => '暂无新闻，点击“新建新闻”开始发布。',
            'columns' => [
                ['attribute' => 'id', 'label' => '编号'],
                ['attribute' => 'title', 'label' => '标题', 'format' => 'raw',
                    'value' => static fn ($news) => Html::a(Html::encode($news->title), ['view', 'id' => $news->id])],
                ['label' => '分类', 'value' => 'category.name'],
                ['label' => '状态', 'value' => 'statusLabel'],
                ['label' => '作者', 'value' => 'author.username'],
                ['attribute' => 'updated_at', 'label' => '修改时间', 'format' => ['datetime', 'php:Y-m-d H:i']],
                ['class' => yii\grid\ActionColumn::class, 'template' => '{view} {update} {delete}',
                    'buttons' => ['delete' => static fn ($url) => Html::a('删除', $url, ['data-method' => 'post', 'data-confirm' => '确认删除这篇新闻？修改记录会保留。'])]],
            ],
        ]) ?>
    </div>
</section>
