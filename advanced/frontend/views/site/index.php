<?php

/** 专题头版，编辑部名称沿用 C 的公开资料。维护：秦东杰。 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\helpers\Url;

/** @var ?common\models\TeamProfile $profile */
$this->title = '绿茵前线 · 2026 美加墨世界杯特别报道';
// TODO: 若小组引入赛程表，以其只读接口替换此处的赛事档案。
$matches = [
    ['06.11', '墨西哥', '南非', '墨西哥城', '2 – 0'],
    ['06.12', '加拿大', '波黑', '多伦多', '1 – 1'],
];
$faq = [
    '哪些国家共同主办本届赛事？' => '加拿大、墨西哥和美国共同主办。',
    '本届世界杯有多少支参赛队？' => '本届扩军到 48 支球队。',
    '小组赛如何分组？' => '48 支球队分成 12 组，每组 4 队。',
    '哪些球队晋级淘汰赛？' => '各组前两名及成绩最好的 8 个小组第三名晋级 32 强。',
    '从揭幕战到决赛一共多少场？' => '赛事合计 104 场，分布在 16 个举办城市。',
    '从哪里查找编辑部发布的文章？' => '进入新闻版，可以按关键词、分类或标签查找已发布的内容。',
];
?>
<article class="wc-front" data-wc-feed="<?= Html::encode(Url::to(['/data/headlines'])) ?>">
    <div class="wc-front-grid">
        <section class="wc-lead">
            <div class="wc-kicker"><i></i> 头版头条 <span> / COVER STORY</span></div>
            <div class="wc-cover">
                <div class="wc-cover-art"><?= Html::img('@web/images/wc2026/stadium.webp', ['alt' => '编辑部绘制的球场俯视插画']) ?><span>球场手记 / 编辑部绘图</span></div>
                <h2 class="wc-cover-title" data-wc-title>世界杯的夏天，<br>从这里开球。</h2>
                <p data-wc-summary>三国、十六城，回看 2026 美加墨世界杯的赛程与故事。</p>
                <?= Html::a('进入新闻版　→', ['/news/index'], ['data-wc-story' => '', 'class' => 'wc-story-link']) ?>
            </div>
        </section>
        <aside class="wc-rail"><div class="wc-kicker"><i></i> 编辑部快讯</div>
            <h2>简讯</h2><div data-wc-news><p>已公开的最新报道将列在这里。</p></div>
            <?= Html::a('查看全部新闻　↗', ['/news/index']) ?>
            <div class="wc-issue"><span>TOURNAMENT ARCHIVE</span><b>06.11 — 07.19</b><p>2026 · 世界杯赛事档案</p></div>
        </aside>
    </div>
    <ul class="wc-stat-band" aria-label="赛事数字"><?php foreach ([['48','参赛球队'], ['104','比赛场次'], ['16','举办城市'], ['3','东道主']] as [$n,$label]): ?>
        <li><b class="wc-stat-num"><?= $n ?></b><span class="wc-stat-lbl"><?= $label ?></span></li><?php endforeach ?></ul>
    <div class="wc-sports">
        <section id="schedule"><div class="wc-kicker"><i></i> MATCH CALENDAR</div><h2>开幕赛程</h2>
            <?php foreach ($matches as [$day,$home,$away,$city]): ?><div class="wc-match"><b><?= $day ?></b><strong><?= $home ?> 对 <?= $away ?></strong><span><?= $city ?></span></div><?php endforeach ?>
        </section>
        <section id="results"><div class="wc-kicker"><i></i> SCOREBOARD</div><h2>完场战报</h2>
            <?php foreach ($matches as [$day,$home,$away,$city,$score]): ?><div class="wc-result"><span><?= $home ?></span><b><?= $score ?></b><span><?= $away ?></span></div><?php endforeach ?>
        </section>
    </div>
    <p class="wc-credit"><?= Html::a('赛程与比分来源 · FIFA 赛事档案 ↗', 'https://www.fifa.com/en/tournaments/mens/worldcup/canadamexicosa2026/articles/match-schedule-fixtures-results-teams-stadiums') ?></p>
    <section class="wc-faq"><div><div class="wc-kicker"><i></i> TOURNAMENT GUIDE</div><h2>看球六问</h2></div>
        <div><?php foreach ($faq as $question => $answer): ?><details class="wc-faq-item"><summary class="wc-faq-q"><?= Html::encode($question) ?><span class="wc-faq-icon" aria-hidden="true">+</span></summary><p class="wc-faq-a"><?= Html::encode($answer) ?></p></details><?php endforeach ?></div>
    </section>
    <?php if ($profile !== null): ?><p class="wc-credit">出品　<?= Html::a(Html::encode($profile->name), ['/team/index']) ?></p><?php endif ?>
</article>
