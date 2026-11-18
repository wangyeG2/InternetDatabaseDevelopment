<?php

/** Shared frontend navigation with news and coursework links. Maintainers: C/B. */

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    ['label' => '首页', 'url' => ['/site/index']],
    ['label' => '编辑部', 'url' => ['/team/index']],
    ['label' => '赛程', 'active' => false, 'url' => ['/site/index', '#' => 'schedule']],
    ['label' => '战报', 'active' => false, 'url' => ['/site/index', '#' => 'results']],
    ['label' => '成员', 'active' => false, 'url' => ['/team/index', '#' => 'roster']],
    ['label' => '新闻公告', 'url' => ['/news/index']],
    ['label' => '读者看台', 'url' => ['/message/index']],
    ['label' => '数据版', 'url' => ['/data/index']],
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
    <div class="container wc-mast">
        <div class="wc-edition">THE TOUCHLINE <i></i> 世界杯特刊</div>
        <?php if (Yii::$app->controller->route === 'site/index'): ?>
            <h1 class="wc-banner-title"><?= Html::a('绿茵前线', ['/site/index'], ['class' => 'wc-name']) ?><span class="visually-hidden"> · <?= Html::encode(Yii::$app->name) ?></span></h1>
        <?php else: ?>
            <?= Html::a('绿茵前线', ['/site/index'], ['class' => 'wc-name']) ?>
        <?php endif ?>
        <div class="wc-dateline"><b>2026 美加墨世界杯特别报道</b><span data-wc-clock>6 月 11 日 — 7 月 19 日</span></div>
    </div>
    <?php NavBar::begin(
        [
            'brandLabel' => false,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-xl navbar-dark wc-nav']
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
