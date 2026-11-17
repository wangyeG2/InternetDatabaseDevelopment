<?php

/** Team and personal coursework downloads. Variable: groups. Maintainer: B. */

declare(strict_types=1);

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $groups */

$this->title = '作业下载';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="py-3">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php foreach ($groups as $group): ?>
        <h2 class="h4 mt-4"><?= Html::encode($group['label']) ?></h2>
        <?php if ($group['items'] === []): ?>
            <p class="text-muted">作业文件尚未上传。</p>
        <?php else: ?>
            <ul class="list-group mb-4">
                <?php foreach ($group['items'] as $item): ?>
                    <li class="list-group-item d-flex justify-content-between flex-wrap gap-2">
                        <?= Html::a(Html::encode($item['name']), ['file', 'group' => $item['group'], 'id' => $item['id']]) ?>
                        <span class="text-muted"><?= Yii::$app->formatter->asShortSize($item['bytes']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endforeach; ?>
</section>
