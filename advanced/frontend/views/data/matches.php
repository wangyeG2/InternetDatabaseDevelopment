<?php

/** All 104 fixtures with stage and group filters. Maintainer: 秦东杰. */

declare(strict_types=1);

use yii\helpers\Html;

$this->title = '全部赛程与赛果';
?>
<article class="wc-archive"><div class="wc-section-head"><div><p>MATCH CENTRE</p><h1>全部赛程与赛果</h1></div></div>
    <?= Html::beginForm(['/data/matches'], 'get', ['class' => 'wc-filters']) ?>
    <label>阶段 <?= Html::dropDownList('stage', $stage, array_combine($stages, $stages), ['prompt' => '全部阶段']) ?></label>
    <label>小组 <?= Html::dropDownList('pool', $pool, array_combine($pools, $pools), ['prompt' => '全部小组']) ?></label>
    <?= Html::submitButton('筛选', ['class' => 'wc-action']) ?>
    <?= Html::endForm() ?>
    <p><?= count($matches) ?> 场比赛 · 比赛地当地日期</p>
    <?php if (!$matches): ?><p>此组合没有比赛。<?= Html::a('查看全部', ['/data/matches']) ?></p><?php endif ?>
    <?php foreach ($matches as $match): ?><?= $this->render('_fixture', ['match' => $match, 'showScore' => true]) ?><?php endforeach ?>
</article>
