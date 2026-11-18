<?php

/** Public detail view. Variable: news (News). Maintainer: B. */

declare(strict_types=1);

use frontend\assets\NewsAsset;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\News $news */

NewsAsset::register($this);
$this->title = $news->title;
$this->params['breadcrumbs'][] = ['label' => '新闻公告', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<article class="news-page news-detail">
    <p class="news-eyebrow"><?= Html::encode($news->category->name) ?></p>
    <h1><?= Html::encode($news->title) ?></h1>
    <p class="news-item-meta">
        <span><?= Html::encode($news->author->username) ?></span>
        <time datetime="<?= date('c', (int) $news->published_at) ?>"><?= Yii::$app->formatter->asDatetime($news->published_at, 'php:Y-m-d H:i') ?></time>
    </p>
    <?php if ($news->summary !== ''):
        ?><p class="news-detail-summary"><?= Html::encode($news->summary) ?></p><?php
    endif; ?>
    <div class="news-body"><?= nl2br(Html::encode($news->content)) ?></div>
    <div class="news-tags mt-4">
        <?php foreach ($news->tags as $tag): ?>
            <?= Html::a('#' . Html::encode($tag->name), ['index', 'tag' => $tag->id]) ?>
        <?php endforeach; ?>
    </div>
    <p><?= Html::a('阅读同栏目报道', ['index', 'category' => $news->category_id]) ?></p>
    <p class="mt-4"><?= Html::a('返回新闻列表', ['index'], ['class' => 'btn btn-outline-secondary']) ?></p>
</article>
