<?php

/** World Cup front page. Maintainer: 秦东杰. */

declare(strict_types=1);

use common\models\CupMatch;
use yii\helpers\Html;
use yii\helpers\Url;

$base = '@web/images/wc2026/';
$final = ($finals[0]['stage'] ?? '') === '决赛' ? $finals[0] : null;
$score = $final ? CupMatch::score($final) : null;
$intro = $final['city'] ?? '';
if ($final && $score['home'] + $score['away'] === 1) {
    $goals = json_decode($final['goals'], true, 512, JSON_THROW_ON_ERROR);
    $goal = $goals[0][0] ?? $goals[1][0] ?? null;
    if ($goal && empty($goal['owngoal'])) {
        $name = $goal['name'] === 'Ferran Torres' ? '费兰·托雷斯' : $goal['name'];
        $intro = '第 ' . $goal['minute'] . ' 分钟，' . $name . '打入全场唯一进球。';
    }
}
$archivePicks = array_values(array_filter($finals, static fn ($match) => $match['stage'] !== '决赛'));
$features = [
    ['champions.webp', '冠军庆祝现场', $final],
    ['england.webp', '英格兰队员庆祝', $archivePicks[0] ?? null],
    ['opening.webp', '世界杯揭幕现场', $opening[0] ?? null],
];
$flag = static fn ($code) => Html::img($base . 'flags/' . $code . '.png', ['class' => 'wc-flag', 'alt' => '']);
?>
<div class="wc-edition-line"><h1><span class="visually-hidden"><?= Html::encode(Yii::$app->name) ?> · </span>2026 美加墨世界杯特别报道</h1><ul class="wc-stat-band" aria-label="赛事数字"><li>48 支球队</li><li>104 场比赛</li></ul></div>
<div class="wc-desk">
    <section class="wc-lead-photo">
        <div class="wc-lead-copy">
            <span class="wc-photo-label">决赛战报<?= $final ? '　/　' . Html::encode(substr($final['match_date'], 5)) : '' ?></span>
            <h2 class="wc-hero-title"><?php if ($score && $score['winner']): ?><span><?= Html::encode($score['winner']) ?></span><span><?= Html::encode($score['decider'] . '夺冠') ?></span><?php else: ?>世界杯决赛现场<?php endif ?></h2>
            <?php if ($final): ?>
                <div class="wc-lead-score"><?= $flag($final['home_code']) ?><?= Html::encode($final['home']) ?> <b><?= Html::encode($score['line']) ?></b> <?= Html::encode($final['away']) ?><?= $flag($final['away_code']) ?></div>
                <p><?= Html::encode($intro) ?></p>
                <?= Html::a('阅读决赛战报', ['/data/match', 'id' => $final['id']], ['class' => 'wc-action']) ?>
            <?php endif ?>
        </div>
        <?= Html::img($base . 'goal.webp', ['class' => 'wc-cover-image', 'alt' => '世界杯门前攻防资料图', 'fetchpriority' => 'high']) ?>
    </section>

    <section id="results" class="wc-recent">
        <div class="wc-desk-heading"><h2>最近赛果</h2><span aria-hidden="true">RESULTS</span><?= Html::a('完整赛程', ['/data/matches'], ['class' => 'wc-more']) ?></div>
        <div class="wc-result-grid">
            <?php foreach ($finals as $m): $result = CupMatch::score($m); ?>
                <a class="wc-result-card" href="<?= Html::encode(Url::to(['/data/match', 'id' => $m['id']])) ?>">
                    <small><?= Html::encode(substr($m['match_date'], 5) . '　' . $m['stage'] . ($result['decider'] ? ' · ' . $result['note'] : '')) ?></small>
                    <?php foreach (['home', 'away'] as $side): ?>
                        <span><?= $flag($m[$side . '_code']) ?><?= Html::encode($m[$side]) ?><b><?= Html::encode($result[$side]) ?></b></span>
                    <?php endforeach ?>
                </a>
            <?php endforeach ?>
        </div>
    </section>

    <aside class="wc-wire">
        <h2>编辑部快讯</h2>
        <?php if ($news): ?>
            <ul><?php foreach ($news as $item): ?>
                <li><time datetime="<?= date('c', (int) $item->published_at) ?>"><?= Html::encode(Yii::$app->formatter->asDate($item->published_at, 'php:m.d')) ?></time><?= Html::a(Html::encode($item->title), ['/news/view', 'id' => $item->id]) ?></li>
            <?php endforeach ?></ul>
        <?php elseif ($archivePicks): ?>
            <p>赛事档案精选</p>
            <ul><?php foreach ($archivePicks as $m):
                $result = CupMatch::score($m);
                $title = $m['home'] . '对阵' . $m['away'];
                if ($result['winner']) {
                    $opponent = $result['winner'] === $m['home'] ? $m['away'] : $m['home'];
                    $title = $result['winner'] . $result['decider'] . '击败' . $opponent;
                    if ($m['stage'] === '季军赛') { $title .= '，夺得季军'; }
                    if ($m['stage'] === '半决赛') { $title .= '，晋级决赛'; }
                }
                ?>
                <li><time datetime="<?= Html::encode($m['match_date']) ?>"><?= Html::encode(substr($m['match_date'], 5) . '　/　' . $m['stage']) ?></time><?= Html::a(Html::encode($title), ['/data/match', 'id' => $m['id']]) ?></li>
            <?php endforeach ?></ul>
        <?php else: ?><p>暂无已发布快讯</p><?php endif ?>
        <?= Html::a('全部新闻　↗', ['/news/index'], ['class' => 'wc-more']) ?>
    </aside>
</div>

<section id="latest" class="wc-section">
    <div class="wc-desk-heading"><h2>镜头里的世界杯</h2><span aria-hidden="true">IN PICTURES</span><?= Html::a('进入新闻版　↗', ['/news/index'], ['class' => 'wc-more']) ?></div>
    <div class="wc-news-grid"><?php foreach ($features as $i => [$photo, $caption, $match]):
        $story = $news[$i] ?? null;
        if (!$story && !$match) { continue; }
        $title = $story ? $story->title : $match['home'] . ' ' . CupMatch::score($match)['line'] . ' ' . $match['away'];
        $url = $story ? ['/news/view', 'id' => $story->id] : ['/data/match', 'id' => $match['id']];
        ?>
        <a class="wc-photo-story" href="<?= Html::encode(Url::to($url)) ?>">
            <?= Html::img($base . $photo, ['alt' => $caption . '资料图', 'loading' => 'lazy']) ?>
            <small><?= $story ? '新闻报道' : Html::encode($match['stage']) ?>　/　<?= Html::encode($story ? Yii::$app->formatter->asDate($story->published_at, 'php:Y.m.d') : $match['match_date']) ?></small>
            <h3><?= Html::encode($title) ?></h3>
        </a>
    <?php endforeach ?></div>
</section>
<section id="schedule" class="wc-section">
    <div class="wc-desk-heading"><h2>开幕赛程</h2><span aria-hidden="true">FIXTURES</span><?= Html::a('浏览赛事档案　↗', ['/data/matches'], ['class' => 'wc-more']) ?></div>
    <div class="wc-fixtures"><?php foreach ($opening as $match): ?>
        <?= $this->render('@frontend/views/data/_fixture', ['match' => $match, 'showScore' => false]) ?>
    <?php endforeach ?></div>
</section>
