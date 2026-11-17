<?php

/** Management detail and revision history. Variables: news, history. Maintainer: B. */

declare(strict_types=1);

use backend\assets\NewsAsset;
use common\models\News;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var common\models\News $news */
/** @var yii\data\ActiveDataProvider $history */

NewsAsset::register($this);
$this->title = $news->title;
$this->params['breadcrumbs'][] = ['label' => '新闻管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="news-admin">
    <h1><?= Html::encode($news->title) ?></h1>
    <p class="text-muted"><?= Html::encode($news->category->name) ?> · <?= Html::encode($news->statusLabel) ?> · 作者 <?= Html::encode($news->author->username) ?></p>
    <?php if ($news->deleted_at === null): ?>
        <div class="news-actions">
            <?= Html::a('编辑', ['update', 'id' => $news->id], ['class' => 'btn btn-primary']) ?>
            <?php if ((int) $news->status !== News::STATUS_PUBLISHED): ?>
                <?= Html::a('发布', ['publish', 'id' => $news->id], ['class' => 'btn btn-success', 'data-method' => 'post']) ?>
            <?php else: ?>
                <?= Html::a('撤回为草稿', ['withdraw', 'id' => $news->id], ['class' => 'btn btn-outline-secondary', 'data-method' => 'post']) ?>
            <?php endif; ?>
            <?= Html::a('删除', ['delete', 'id' => $news->id], ['class' => 'btn btn-outline-danger', 'data-method' => 'post', 'data-confirm' => '确认删除？修改记录会保留。']) ?>
            <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    <?php endif; ?>
    <?php if ($news->category->is_active == 0):
        ?><p class="alert alert-warning">该分类已停用，所属新闻不会在前台展示。</p><?php
    endif; ?>
    <?php if ($news->summary !== ''):
        ?><p class="news-admin-summary"><?= Html::encode($news->summary) ?></p><?php
    endif; ?>
    <div class="news-admin-body"><?= nl2br(Html::encode($news->content)) ?></div>
    <p class="mt-3">标签：<?= Html::encode(implode('、', $news->getTagNames())) ?: '无' ?></p>
    <h2 class="h4 mt-5">修改记录</h2>
    <?= ListView::widget([
        'dataProvider' => $history,
        'itemOptions' => ['class' => 'news-revision'],
        'itemView' => static function ($revision): string {
            $snapshot = Json::decode($revision->snapshot);
            return '<details><summary>' . Html::encode($revision->operationLabel . ' · ' . $revision->editor->username . ' · '
                . Yii::$app->formatter->asDatetime($revision->created_at, 'php:Y-m-d H:i:s')) . '</summary>'
                . '<h3 class="h6 mt-3">' . Html::encode($snapshot['title']) . '</h3>'
                . '<div class="news-admin-body">' . nl2br(Html::encode($snapshot['content'])) . '</div></details>';
        },
        'layout' => '{items}<div class="mt-3">{pager}</div>',
        'emptyText' => '暂无修改记录。',
    ]) ?>
</section>
