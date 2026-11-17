<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use common\assets\TeamAsset;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ?common\models\TeamProfile $profile */
TeamAsset::register($this);
$this->title = Yii::$app->name;
?>
<div class="team-page">
<header class="team-masthead">
<p class="team-eyebrow">2026 / WORLD CUP / COURSE PROJECT</p>
<h1><?= Html::encode($profile !== null ? $profile->name : Yii::$app->name) ?></h1>
<p class="team-lead"><?= Html::encode(($profile !== null && $profile->summary !== '') ? $profile->summary : '这是一个以2026世界杯为主题的互联网数据库课程团队项目网站。') ?></p>
<p><?= Html::encode(($profile !== null && $profile->motto !== '') ? $profile->motto : '以协作连接每一位成员，以记录呈现项目进展。') ?></p>
</header>
<h2>从这里进入</h2>
<div class="team-row"><span class="team-eyebrow">01 / TEAM</span>
<h3><?= Html::a('团队介绍', ['/team/index']) ?></h3><p>认识团队成员，了解分工与项目时间线。</p></div>
<div class="team-row"><span class="team-eyebrow">02 / NEWS</span>
<h3><?= Html::a('新闻公告', ['/news/index']) ?></h3><p>阅读已经公开发布的新闻与课程公告。</p></div>
<div class="team-row"><span class="team-eyebrow">03 / FILES</span>
<h3><?= Html::a('作业下载', ['/download/index']) ?></h3><p>查看当前提供的团队与个人作业文件。</p></div>
</div>
