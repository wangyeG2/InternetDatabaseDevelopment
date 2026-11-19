<?php

/** 专题头版，编辑部名称沿用 C 的公开资料。维护：秦东杰。 */

declare(strict_types=1);

use yii\helpers\Html;

/** @var ?common\models\TeamProfile $profile */
$this->title = '绿茵前线 · 2026 美加墨世界杯特别报道';
$faq = [
    '哪些国家共同主办本届赛事？' => '加拿大、墨西哥和美国共同主办。',
    '本届世界杯有多少支参赛队？' => '本届扩军到 48 支球队。',
    '小组赛如何分组？' => '48 支球队分成 12 组，每组 4 队。',
    '哪些球队晋级淘汰赛？' => '各组前两名及成绩最好的 8 个小组第三名晋级 32 强。',
    '从揭幕战到决赛一共多少场？' => '赛事合计 104 场，分布在 16 个举办城市。',
    '从哪里查找编辑部发布的文章？' => '进入新闻版，可以按关键词、分类或标签查找已发布的内容。',
];
?>
<article class="wc-front">
    <?= \frontend\widgets\TournamentEdition::widget() ?>
    <section class="wc-faq"><div><div class="wc-kicker"><i></i> TOURNAMENT GUIDE</div><h2>看球六问</h2></div>
        <div><?php foreach ($faq as $question => $answer): ?><details class="wc-faq-item"><summary class="wc-faq-q"><?= Html::encode($question) ?><span class="wc-faq-icon" aria-hidden="true">+</span></summary><p class="wc-faq-a"><?= Html::encode($answer) ?></p></details><?php endforeach ?></div>
    </section>
    <?php if ($profile !== null): ?><p class="wc-credit">出品　<?= Html::a(Html::encode($profile->name), ['/team/index']) ?></p><?php endif ?>
</article>
