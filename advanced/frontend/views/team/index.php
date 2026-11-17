<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use common\assets\TeamAsset;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ?common\models\TeamProfile $profile */
/** @var common\models\TeamMember[] $members */
/** @var common\models\TeamMilestone[] $milestones */
/** @var common\models\TeamLink[] $links */
TeamAsset::register($this);
$this->title = '团队介绍';
?>
<article class="team-page">
<header class="team-masthead">
<p class="team-eyebrow">2026 / WORLD CUP / COURSE PROJECT</p>
<h1><?= Html::encode($profile !== null ? $profile->name : '团队介绍') ?></h1>
<?php if ($profile !== null): ?>
<p class="team-lead"><?= Html::encode($profile->motto) ?></p>
<p class="team-lead"><?= Html::encode($profile->summary) ?></p>
<div class="team-prose"><?= Html::encode($profile->body) ?></div>
<p><?= Html::encode($profile->theme_note) ?></p>
<?php else: ?>
<p class="team-empty">团队资料尚未发布。</p>
<?php endif ?>
</header>
<section id="roster"><h2>成员</h2>
<?php if ($members === []):
    ?><p class="team-empty">暂无公开成员资料。</p><?php
endif ?>
<ol class="team-roster">
<?php foreach ($members as $member): ?>
<li class="team-row">
<span class="team-eyebrow"><?= Html::encode($member->module_code) ?></span>
<div><h3><?= Html::a($member->name, ['member', 'slug' => $member->slug]) ?></h3>
<p><?= Html::encode($member->role_title) ?></p></div>
<p><?= Html::encode($member->duty) ?></p>
</li>
<?php endforeach ?>
</ol></section>
<section><h2>项目时间线</h2>
<?php if ($milestones === []):
    ?><p class="team-empty">暂无公开项目节点。</p><?php
endif ?>
<?php foreach ($milestones as $milestone): ?>
<div class="team-row"><time><?= Html::encode($milestone->happened_on) ?></time>
<h3><?= Html::encode($milestone->title) ?><?= $milestone->is_example ? '（示例）' : '' ?></h3>
<p class="team-prose"><?= Html::encode($milestone->summary) ?></p></div>
<?php endforeach ?>
</section>
<section><h2>相关链接</h2>
<?php if ($links === []):
    ?><p class="team-empty">暂无公开链接。</p><?php
endif ?>
<div class="team-links">
<?php foreach ($links as $link): ?>
    <?php if (preg_match('~^https?://~i', $link->url)): ?>
        <?= Html::a($link->label, $link->url, ['rel' => 'external noopener noreferrer', 'target' => '_blank']) ?>
    <?php endif ?>
<?php endforeach ?>
</div></section>
</article>
