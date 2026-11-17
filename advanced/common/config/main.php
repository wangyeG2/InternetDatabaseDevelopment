<?php

/** Shared application identity and components. Maintainers: C/B. */

declare(strict_types=1);

return [
    'name' => '2026世界杯主题团队主页',
    'bootstrap' => [
        \common\bootstrap\MailerBootstrap::class,
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
    ],
];
