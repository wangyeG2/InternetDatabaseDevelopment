<?php

/** Match report with regulation, extra-time and shootout scores. Maintainer: 秦东杰. */

declare(strict_types=1);

use common\models\CupMatch;
use yii\helpers\Html;

$this->title = $match['home'] . ' 对 ' . $match['away'];
$scores = json_decode($match['scores'], true, 512, JSON_THROW_ON_ERROR);
$goals = json_decode($match['goals'], true, 512, JSON_THROW_ON_ERROR);
?>
<article class="wc-match-page">
    <?= Html::a('← 全部赛程与赛果', ['/data/matches']) ?>
    <div class="wc-section-head"><div><p><?= Html::encode($match['stage'] . ($match['pool'] ? ' · ' . $match['pool'] . ' 组' : '')) ?></p><h1><?= Html::encode($this->title) ?></h1></div></div>
    <div class="wc-scoreboard wc-detail-score"><div class="wc-score-face">
        <?php foreach (['home', 'away'] as $side): ?>
            <?php if ($side === 'away'): ?><b><?= Html::encode(CupMatch::score($match)['line']) ?></b><?php endif ?>
            <span><?= Html::img('@web/images/wc2026/flags/' . $match[$side . '_code'] . '.png', ['alt' => '']) ?><?= Html::encode($match[$side]) ?></span>
        <?php endforeach ?>
    </div><p><?= Html::encode($match['match_date'] . ' · ' . $match['local_time'] . ' · ' . $match['city']) ?></p></div>
    <section><h2>比分记录</h2><?php foreach (['ht' => '半场', 'ft' => '常规时间', 'et' => '加时完场', 'p' => '点球大战'] as $key => $label): ?>
        <?php if (isset($scores[$key])): ?><p><?= $label ?>　<strong><?= Html::encode(implode(' : ', $scores[$key])) ?></strong></p><?php endif ?>
    <?php endforeach ?></section>
    <?php if (!$goals[0] && !$goals[1] && $goals[0] === null): ?><p>这场比赛的资料未附进球明细。</p><?php endif ?>
    <?php if ($goals[0] || $goals[1]): ?><section><h2>进球记录</h2>
        <?php foreach ($goals as $i => $teamGoals): if (!$teamGoals) { continue; } ?>
            <h3><?= Html::encode($i ? $match['away'] : $match['home']) ?></h3>
            <ul><?php foreach ($teamGoals as $goal): ?><li><?= Html::encode($goal['minute'] . '′　' . $goal['name']) ?><?= !empty($goal['owngoal']) ? '（乌龙）' : (!empty($goal['penalty']) ? '（点球）' : '') ?></li><?php endforeach ?></ul>
        <?php endforeach ?></section><?php endif ?>
</article>
