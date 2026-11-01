<?php

/** One public news row. Variable: model (News). Maintainer: B. */

declare(strict_types=1);

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\News $model */
?>
<article class="news-item">
    <div class="news-item-meta">
        <span><?= Html::encode($model->category->name) ?></span>
        <time datetime="<?= date('c', (int) $model->published_at) ?>"><?= Yii::$app->formatter->asDate($model->published_at, 'php:Y-m-d') ?></time>
        <?php if ($model->is_pinned):
            ?><span class="news-pin">置顶</span><?php
        endif; ?>
    </div>
    <h2><?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?></h2>
    <?php if ($model->summary !== ''):
        ?><p class="news-summary"><?= Html::encode($model->summary) ?></p><?php
    endif; ?>
    <div class="news-tags">
        <?php foreach ($model->tags as $tag): ?>
            <?= Html::a('#' . Html::encode($tag->name), ['index', 'tag' => $tag->id]) ?>
        <?php endforeach; ?>
    </div>
</article>
