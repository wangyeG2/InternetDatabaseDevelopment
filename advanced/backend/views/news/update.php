<?php

/** Edit news page. Variables: model, categories. Maintainer: B. */
declare(strict_types=1);

use backend\assets\NewsAsset;
use yii\helpers\Html;

NewsAsset::register($this);
$this->title = '编辑新闻';
$this->params['breadcrumbs'][] = ['label' => '新闻管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="news-admin"><h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model, 'categories' => $categories]) ?>
</section>
