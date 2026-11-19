<?php

/** Fixture archive row. Maintainer: 秦东杰. */

declare(strict_types=1);

use common\models\CupMatch;
use yii\helpers\Html;
use yii\helpers\Url;

$score = CupMatch::score($match);
?>
<a class="wc-fixture" href="<?= Html::encode(Url::to(['/data/match', 'id' => $match['id']])) ?>">
    <time datetime="<?= Html::encode($match['match_date']) ?>"><b><?= substr($match['match_date'], 8) ?></b><?= (int) substr($match['match_date'], 5, 2) ?> 月</time>
    <span class="wc-stage"><?= Html::encode($match['pool'] ? $match['pool'] . ' 组' : $match['stage']) ?></span>
    <span class="wc-contest">
        <span class="wc-home"><?= Html::encode($match['home']) ?><?= Html::img('@web/images/wc2026/flags/' . $match['home_code'] . '.png', ['alt' => '', 'loading' => 'lazy']) ?></span>
        <strong><?= Html::encode($showScore ? $score['line'] : substr($match['local_time'], 0, 5)) ?></strong>
        <span class="wc-away"><?= Html::img('@web/images/wc2026/flags/' . $match['away_code'] . '.png', ['alt' => '', 'loading' => 'lazy']) ?><?= Html::encode($match['away']) ?></span>
    </span>
    <span class="wc-ground"><?= Html::encode($match['city']) ?></span>
    <small><?= Html::encode($showScore ? $score['note'] : substr($match['local_time'], 6)) ?></small>
</a>
