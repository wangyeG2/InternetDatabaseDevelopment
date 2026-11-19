<?php

/** Responsive queue for publication and hiding of messages. Maintainer: D (秦东杰). */

declare(strict_types=1);

use common\models\Message;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $filter */
/** @var yii\data\ActiveDataProvider $messages */

$this->title = '留言管理';
?>
<div class="container py-4">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-body-secondary">公开后的留言会进入前台列表和话题统计。</p>
    <nav class="nav nav-pills gap-2 flex-wrap mb-4" aria-label="筛选留言状态">
        <?php foreach (Message::STATUSES + ['all' => null] as $key => $status): ?>
            <?= Html::a($status === null ? '全部' : Message::STATUS_LABELS[$status], ['index', 'status' => $key], [
                'class' => 'nav-link' . ($filter === $key ? ' active' : ''),
                'aria-current' => $filter === $key ? 'page' : null,
            ]) ?>
        <?php endforeach; ?>
    </nav>
    <p class="text-body-secondary">共 <?= $messages->getTotalCount() ?> 条留言</p>
    <?php if ($messages->getCount() === 0): ?>
        <p class="card card-body text-body-secondary">此状态下暂无留言。</p>
    <?php endif; ?>
    <?php foreach ($messages->getModels() as $message): ?>
        <?php /** @var Message $message */ ?>
        <article class="card mb-3" id="message-<?= $message->id ?>">
            <div class="card-body">
                <header class="d-flex gap-2 align-items-center flex-wrap">
                    <strong class="text-break"><?= Html::encode($message->author?->username ?? '编辑部示例') ?></strong>
                    <span class="badge bg-secondary"><?= Html::encode(Message::TOPICS[$message->topic] ?? '留言') ?></span>
                    <span class="badge bg-info text-dark"><?= Html::encode(Message::STATUS_LABELS[$message->status]) ?></span>
                    <time class="text-body-secondary" datetime="<?= date(DATE_ATOM, $message->created_at) ?>">
                        <?= Html::encode(Yii::$app->formatter->asDatetime($message->created_at, 'php:Y-m-d H:i')) ?>
                    </time>
                </header>
                <p class="text-break mt-3"><?= nl2br(Html::encode($message->body)) ?></p>
                <div class="d-flex gap-2 flex-wrap">
                    <?php if ($message->status !== Message::PUBLISHED): ?>
                        <?= Html::beginForm(['review', 'id' => $message->id, 'filter' => $filter], 'post', [
                            'class' => 'message-publish-form',
                        ]) ?>
                        <?= Html::hiddenInput('originalStatus', (string) $message->status) ?>
                        <?= Html::submitButton('公开留言', ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::endForm() ?>
                    <?php endif; ?>
                    <?php if ($message->status !== Message::HIDDEN): ?>
                        <?= Html::beginForm(['hide', 'id' => $message->id, 'filter' => $filter], 'post', [
                            'class' => 'message-hide-form',
                        ]) ?>
                        <?= Html::hiddenInput('originalStatus', (string) $message->status) ?>
                        <?= Html::submitButton('隐藏留言', ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                        <?= Html::endForm() ?>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
    <?= LinkPager::widget(['pagination' => $messages->getPagination()]) ?>
</div>
