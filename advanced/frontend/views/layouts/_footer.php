<?php

/** 特刊报尾与课程资料入口。维护：秦东杰。 */

declare(strict_types=1);

use yii\helpers\Html;
?>
<footer class="wc-footer mt-auto">
    <div class="container"><div><b>绿茵前线</b>　© <?= Html::encode(Yii::$app->name) ?></div>
        <div><?= Html::a('团队介绍', ['/team/index']) ?>　<?= Html::a('团队作业', ['/download/index', '#' => '团队作业']) ?>　<?= Html::a('个人作业', ['/download/index', '#' => '个人作业']) ?></div>
        <small>模板设计　<?= Html::a('Arena · uiCookies', 'https://uicookies.com/downloads/arena/') ?>　/　<?= Html::a('Kilnfield · 8B', 'https://8b.com/') ?></small>
    </div>
</footer>
