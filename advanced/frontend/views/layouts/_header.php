<?php

/** Shared frontend navigation with news and coursework links. Maintainers: C/B. */

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    ['label' => '首页', 'url' => ['/site/index']],
    ['label' => '团队介绍', 'url' => ['/team/index']],
    ['label' => '成员', 'url' => ['/team/index', '#' => 'roster']],
    ['label' => '新闻公告', 'url' => ['/news/index']],
    ['label' => '作业下载', 'url' => ['/download/index']],
    ['label' => '关于本站', 'url' => ['/site/about']],
    ['label' => '联系', 'url' => ['/site/contact']],
    [
        'label' => '注册',
        'url' => ['/site/signup'],
        'visible' => Yii::$app->user->isGuest,
    ],
    [
        'label' => '登录',
        'url' => ['/site/login'],
        'visible' => Yii::$app->user->isGuest,
    ],
    [
        'label' => '退出 (' . Html::encode(Yii::$app->user->identity?->username) . ')',
        'url' => ['/site/logout'],
        'linkOptions' => [
            'data-method' => 'post',
            'class' => 'logout',
        ],
        'visible' => !Yii::$app->user->isGuest,
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => '世界杯 · 课程团队',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-xl navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        '&#127769;',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Switch to dark mode',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
