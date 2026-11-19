<?php

/** Arena-style cover, fixtures, results and news. Maintainer: 秦东杰. */

declare(strict_types=1);

use common\models\CupMatch;
use yii\helpers\Html;
use yii\helpers\Url;

$base = '@web/images/wc2026/';
$final = ($finals[0]['stage'] ?? '') === '决赛' ? $finals[0] : null;
$finalScore = $final ? CupMatch::score($final) : null;
$features = [
    ['goal.webp', '门前攻防', $quarter[1] ?? null],
    ['england.webp', '淘汰赛现场', $quarter[2] ?? null],
    ['opening.webp', '揭幕战现场', $opening[0] ?? null],
];
?>
<div class="wc-lead-grid">
    <section class="wc-lead-photo">
        <?= Html::img($base . 'champions.webp', ['alt' => '西班牙队在冠军庆祝现场', 'fetchpriority' => 'high']) ?>
        <div class="wc-lead-overlay">
            <span class="wc-photo-label">决赛特写</span>
            <h2 class="wc-hero-title"><?php if ($finalScore && $finalScore['winner']): ?><span><?= Html::encode($finalScore['winner']) ?></span><span><?= Html::encode($finalScore['decider'] . '夺冠') ?></span><?php else: ?>世界杯决赛现场<?php endif ?></h2>
            <?php if ($final): ?>
                <p><?= Html::encode($final['home'] . ' ' . $finalScore['line'] . ' ' . $final['away']) ?>　<span>｜</span>　<?= Html::encode($final['city']) ?></p>
                <?= Html::a('决赛战报　→', ['/data/match', 'id' => $final['id']], ['class' => 'wc-action']) ?>
            <?php endif ?>
        </div>
    </section>
    <aside class="wc-wire"><h2>编辑部快讯</h2>
        <?php if ($news): ?>
            <ul><?php foreach ($news as $item): ?>
                <li><time><?= Html::encode(date('m.d', (int) $item->published_at)) ?></time><?= Html::a(Html::encode($item->title), ['/news/view', 'id' => $item->id]) ?></li>
            <?php endforeach ?></ul>
        <?php else: ?>
            <p class="wc-wire-empty">暂无已发布快讯</p>
            <ul aria-label="最新完场"><?php foreach ($finals as $m): ?>
                <li><time><?= Html::encode(substr($m['match_date'], 5)) ?> · <?= Html::encode($m['stage']) ?></time><?= Html::a(Html::encode($m['home'] . ' ' . CupMatch::score($m)['line'] . ' ' . $m['away']), ['/data/match', 'id' => $m['id']]) ?></li>
            <?php endforeach ?></ul>
        <?php endif ?>
        <?= Html::a('全部新闻　↗', ['/news/index'], ['class' => 'wc-more']) ?>
    </aside>
</div>
<ul class="wc-stat-band" aria-label="赛事数字"><?php foreach ([['48','参赛球队'], ['104','比赛场次'], ['16','举办城市'], ['3','东道主']] as [$number,$label]): ?>
    <li><b class="wc-stat-num"><?= $number ?></b><span class="wc-stat-lbl"><?= $label ?></span></li><?php endforeach ?></ul>
<section id="schedule" class="wc-section">
    <div class="wc-section-head"><div><p>FIXTURES</p><h2>开幕赛程</h2></div><?= Html::a('完整赛程', ['/data/matches'], ['class' => 'wc-more']) ?></div>
    <div class="wc-fixtures"><?php foreach ($opening as $match): ?>
        <?= $this->render('@frontend/views/data/_fixture', ['match' => $match, 'showScore' => false]) ?>
    <?php endforeach ?></div>
</section>
<section id="results" class="wc-section wc-scoreboard">
    <div class="wc-section-head"><div><p>RESULTS</p><h2>最新赛果</h2></div><?= Html::a('全部赛果　↗', ['/data/matches'], ['class' => 'wc-more']) ?></div>
    <div class="wc-result-grid"><?php foreach ($finals as $m): $score = CupMatch::score($m); ?>
        <a class="wc-score-card" href="<?= Html::encode(Url::to(['/data/match', 'id' => $m['id']])) ?>">
            <div class="wc-score-meta"><strong><?= Html::encode($m['stage']) ?></strong><time><?= Html::encode(substr($m['match_date'], 5)) ?></time></div>
            <div class="wc-score-face">
                <span><?= Html::img($base . 'flags/' . $m['home_code'] . '.png', ['alt' => '']) ?><?= Html::encode($m['home']) ?></span>
                <b><?= Html::encode($score['line']) ?></b>
                <span><?= Html::img($base . 'flags/' . $m['away_code'] . '.png', ['alt' => '']) ?><?= Html::encode($m['away']) ?></span>
            </div>
            <small><?= Html::encode($score['note'] . ' · ' . $m['city']) ?></small>
        </a>
    <?php endforeach ?></div>
</section>
<section id="latest" class="wc-section">
    <div class="wc-section-head"><div><p>LATEST NEWS</p><h2>最新消息</h2></div><?= Html::a('进入新闻版　↗', ['/news/index'], ['class' => 'wc-more']) ?></div>
    <div class="wc-news-grid"><?php foreach ($features as $i => [$photo,$caption,$match]):
        $story = $news[$i] ?? null;
        if (!$story && !$match) { continue; }
        $title = $story ? $story->title : $match['home'] . ' ' . CupMatch::score($match)['line'] . ' ' . $match['away'];
        $url = $story ? ['/news/view', 'id' => $story->id] : ['/data/match', 'id' => $match['id']];
        ?>
        <a class="wc-photo-story" href="<?= Html::encode(Url::to($url)) ?>">
            <?= Html::img($base . $photo, ['alt' => $caption . '资料图', 'loading' => 'lazy']) ?>
            <small><?= $story ? '新闻报道' : Html::encode($match['stage']) ?>　/　<?= Html::encode($story ? date('Y.m.d', (int) $story->published_at) : $match['match_date']) ?></small>
            <h3><?= Html::encode($title) ?></h3>
        </a>
    <?php endforeach ?></div>
</section>
