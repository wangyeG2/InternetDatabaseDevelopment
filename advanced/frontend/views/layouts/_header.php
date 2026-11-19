<?php

/** Shared public navigation and course links. Maintainer: 秦东杰. */

declare(strict_types=1);

use yii\bootstrap5\BootstrapPluginAsset;
use yii\bootstrap5\Nav;
use yii\helpers\Html;

BootstrapPluginAsset::register($this);
$items = [
    ['label' => '首页', 'url' => ['/site/index']],
    ['label' => '赛程赛果', 'options' => ['class' => 'd-none d-xl-block'], 'url' => ['/data/matches'], 'active' => in_array(Yii::$app->controller->route, ['data/matches', 'data/match'], true)],
    ['label' => '新闻公告', 'url' => ['/news/index']],
    ['label' => '读者看台', 'url' => ['/message/index']],
    ['label' => '数据版', 'url' => ['/data/index']],
    ['label' => '编辑部与作业', 'items' => [
        ['label' => '编辑部', 'url' => ['/team/index']],
        ['label' => '成员', 'url' => ['/team/index', '#' => 'roster']],
        ['label' => '作业下载', 'url' => ['/download/index']],
    ]],
    ['label' => '注册', 'url' => ['/site/signup'], 'visible' => Yii::$app->user->isGuest],
    ['label' => '登录', 'url' => ['/site/login'], 'visible' => Yii::$app->user->isGuest],
    [
        'label' => '退出 (' . Yii::$app->user->identity?->username . ')',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post', 'class' => 'logout'],
        'visible' => !Yii::$app->user->isGuest,
    ],
];
?>
<header id="header">
    <nav class="navbar navbar-expand-xl navbar-dark wc-nav" aria-label="主导航">
        <div class="container">
            <?= Html::a('<i aria-hidden="true"></i>绿茵前线', ['/site/index'], ['class' => 'wc-name']) ?>
            <?= Html::a('赛程赛果', ['/data/matches'], ['class' => 'wc-nav-shortcut d-xl-none']) ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#wc-menu" aria-controls="wc-menu" aria-expanded="false" aria-label="展开或收起主导航"><span class="navbar-toggler-icon"></span></button>
            <div id="wc-menu" class="collapse navbar-collapse">
                <?= Nav::widget(['options' => ['class' => 'navbar-nav ms-auto'], 'items' => $items]) ?>
                <?= Html::button('&#127769;', ['id' => 'theme-toggle', 'class' => 'btn btn-link nav-link', 'aria-label' => '切换配色']) ?>
            </div>
        </div>
    </nav>
</header>
