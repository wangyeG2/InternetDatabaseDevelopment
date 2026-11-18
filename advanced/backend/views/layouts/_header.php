<?php

/** Shared backend navigation with news management entry. Maintainers: C/B. */

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    ['label' => '团队资料', 'url' => ['/team-profile/update'],
        'visible' => Yii::$app->user->identity instanceof \common\models\User && Yii::$app->user->identity->isAdmin()],
    ['label' => '成员管理', 'url' => ['/team-member/index'],
        'visible' => Yii::$app->user->identity instanceof \common\models\User && Yii::$app->user->identity->isAdmin()],
    ['label' => '项目时间线', 'url' => ['/team-milestone/index'],
        'visible' => Yii::$app->user->identity instanceof \common\models\User && Yii::$app->user->identity->isAdmin()],
    ['label' => '相关链接', 'url' => ['/team-link/index'],
        'visible' => Yii::$app->user->identity instanceof \common\models\User && Yii::$app->user->identity->isAdmin()],

    ['label' => '新闻管理', 'url' => ['/news/index'],
        'visible' => Yii::$app->user->identity instanceof \common\models\User && Yii::$app->user->identity->isAdmin()],
    ['label' => '留言管理', 'url' => ['/message/index'],
        'visible' => Yii::$app->user->identity instanceof \common\models\User && Yii::$app->user->identity->isAdmin()],
    [
        'label' => '首页',
        'url' => ['/site/index'],
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
