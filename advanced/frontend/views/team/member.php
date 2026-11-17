<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use common\assets\TeamAsset;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TeamMember $member */
TeamAsset::register($this);
$this->title = $member->name . ' · 成员介绍';
?>
<article class="team-page">
<header class="team-masthead">
<p class="team-eyebrow">TEAM / <?= Html::encode($member->module_code) ?></p>
<h1><?= Html::encode($member->name) ?></h1>
<p class="team-lead"><?= Html::encode($member->role_title) ?></p>
<p><?= Html::encode($member->duty) ?></p>
</header>
<h2>个人介绍</h2>
<div class="team-prose"><?= Html::encode($member->bio) ?></div>
<h2>职责分工</h2>
<?php if ($member->duties === []):
    ?><p class="team-empty">暂无额外职责说明。</p><?php
endif ?>
<?php foreach ($member->duties as $duty): ?>
<h3><?= Html::encode($duty->title) ?></h3>
<p class="team-prose"><?= Html::encode($duty->detail) ?></p>
<?php endforeach ?>
<?= Html::a('返回团队介绍', ['index', '#' => 'roster']) ?>
</article>
