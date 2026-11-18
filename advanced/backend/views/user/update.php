<?php

/** Administrator account editor. Variable: model. Maintainer: A. */

declare(strict_types=1);

use common\models\User;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserManagementForm $model */

$this->title = '管理用户：' . $model->record->username;
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-admin">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>用户：<?= Html::encode($model->record->username) ?>（<?= Html::encode($model->record->email) ?>）</p>
    <?= $this->render('_form', ['model' => $model]) ?>
</section>
