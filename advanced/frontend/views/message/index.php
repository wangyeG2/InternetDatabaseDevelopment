<?php

/** Frontend message form, public list and topic chart. Maintainer: D (秦东杰). */

declare(strict_types=1);

use common\models\Message;
use frontend\assets\WorldCupAsset;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Message $model */
/** @var yii\data\ActiveDataProvider $messages */
/** @var array<string, int> $topicCounts */

$this->title = '世界杯留言板';
$this->registerCssFile('@web/css/message.css', ['depends' => WorldCupAsset::class]);
$this->registerJsFile('@web/js/message.js', ['depends' => WorldCupAsset::class]);
$total = array_sum($topicCounts);
?>
<div class="message-board">
    <header class="message-hero">
        <h1>读者看台</h1>
        <p>聊比赛、交流项目，也欢迎给网站提建议。</p>
        <a class="btn btn-light" href="#message-compose">写留言</a>
    </header>

    <div class="message-columns">
        <section class="message-list" aria-labelledby="public-messages-title">
            <h2 id="public-messages-title">公开留言 <span class="message-total"><?= $total ?></span></h2>
            <?php if ($messages->getCount() === 0): ?>
                <p class="message-card message-empty">暂无公开留言。审核通过的留言会在这里显示。</p>
            <?php endif; ?>
            <?php foreach ($messages->getModels() as $message): ?>
                <?php /** @var Message $message */ ?>
                <article class="message-card">
                    <header class="message-meta">
                        <strong><?= Html::encode($message->author?->username ?? '编辑部示例') ?></strong>
                        <span class="message-topic"><?= Html::encode(Message::TOPICS[$message->topic] ?? '留言') ?></span>
                        <time datetime="<?= date(DATE_ATOM, $message->created_at) ?>">
                            <?= Html::encode(Yii::$app->formatter->asDatetime($message->created_at, 'php:Y-m-d H:i')) ?>
                        </time>
                    </header>
                    <p class="message-copy"><?= Html::encode($message->body) ?></p>
                </article>
            <?php endforeach; ?>
            <?= LinkPager::widget(['pagination' => $messages->getPagination()]) ?>
        </section>

        <div class="message-side">
            <section id="message-compose" class="message-card" aria-labelledby="message-compose-title">
                <h2 id="message-compose-title">写留言</h2>
                <?php if (Yii::$app->user->isGuest): ?>
                    <p>登录后即可提交留言，审核通过后会展示你的用户名和留言内容。</p>
                    <?= Html::a('登录', ['/site/login'], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('注册账号', ['/site/signup'], ['class' => 'btn btn-outline-secondary']) ?>
                <?php else: ?>
                    <?= $this->render('_form', ['model' => $model]) ?>
                <?php endif; ?>
            </section>

            <section class="message-card message-chart" data-message-chart aria-labelledby="chart-title">
                <div class="message-chart-head">
                    <h2 id="chart-title">话题热度</h2>
                    <div class="btn-group btn-group-sm" role="group" aria-label="图表指标" data-chart-controls hidden>
                        <button type="button" class="btn btn-outline-secondary active" data-chart-mode="count"
                                aria-pressed="true">条数</button>
                        <button type="button" class="btn btn-outline-secondary" data-chart-mode="share"
                                aria-pressed="false">占比</button>
                    </div>
                </div>
                <p class="message-helper" data-chart-summary aria-live="polite">已公开 <?= $total ?> 条留言，按话题显示条数。</p>
                <ul class="message-bars">
                    <?php foreach ($topicCounts as $topic => $count): ?>
                        <li data-chart-count="<?= $count ?>">
                            <div class="message-bar-label">
                                <span><?= Html::encode(Message::TOPICS[$topic]) ?></span>
                                <strong data-chart-value><?= $count ?> 条</strong>
                            </div>
                            <div class="message-track" aria-hidden="true">
                                <span style="--bar-width: <?= $count * 100 / max(1, max($topicCounts)) ?>%"></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </div>
    </div>
</div>
