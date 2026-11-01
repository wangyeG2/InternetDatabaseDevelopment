<?php

/** Public list view. Variables: search, dataProvider, categories. Maintainer: B. */

declare(strict_types=1);

use frontend\assets\NewsAsset;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var common\models\NewsSearch $search */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array<int, string> $categories */

NewsAsset::register($this);
$this->title = '新闻公告';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="news-page">
    <header class="news-heading">
        <p class="news-eyebrow">WORLD CUP 2026 · TEAM JOURNAL</p>
        <h1>新闻公告</h1>
        <p>关注赛事主题，记录团队进展。</p>
    </header>
    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['index'], 'options' => ['class' => 'news-filters']]); ?>
    <?= Html::errorSummary($search, ['class' => 'alert alert-danger']) ?>
    <div class="row g-3 align-items-end">
        <div class="col-md-7"><?= Html::label('搜索标题与摘要', 'news-keyword', ['class' => 'form-label']) ?>
            <?= Html::textInput('q', $search->q, ['id' => 'news-keyword', 'class' => 'form-control', 'maxlength' => 120, 'placeholder' => '输入关键词']) ?></div>
        <div class="col-md-3"><?= Html::label('分类', 'news-category', ['class' => 'form-label']) ?>
            <?= Html::dropDownList('category', $search->category, $categories, ['id' => 'news-category', 'class' => 'form-select', 'prompt' => '全部分类']) ?></div>
        <div class="col-md-2"><?= Html::submitButton('筛选', ['class' => 'btn btn-success w-100']) ?></div>
    </div>
    <?php if ($search->tag !== ''): ?>
        <p class="mt-3 mb-0">正在按标签筛选。<?= Html::a('清除筛选', ['index']) ?></p>
    <?php endif; ?>
    <?php ActiveForm::end(); ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_item',
        'itemOptions' => ['tag' => false],
        'layout' => '<div class="news-count">{summary}</div>{items}<nav class="mt-4" aria-label="新闻分页">{pager}</nav>',
        'summary' => '共 {totalCount} 篇 · 第 {begin}-{end} 篇',
        'emptyText' => '暂无符合条件的新闻公告。',
        'emptyTextOptions' => ['class' => 'news-empty'],
        'pager' => ['class' => yii\bootstrap5\LinkPager::class],
    ]) ?>
</section>
