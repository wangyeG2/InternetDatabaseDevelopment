<?php

/** Category creation/editing view. Variable: model (NewsCategoryForm). Maintainer: B. */
declare(strict_types=1);

use backend\assets\NewsAsset;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

NewsAsset::register($this);
$this->title = $model->record === null ? '新建分类' : '编辑分类';
$this->params['breadcrumbs'][] = ['label' => '新闻分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="news-admin">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(['id' => 'news-category-form']); ?>
    <?= $form->errorSummary($model) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 60]) ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => 80])->hint('使用小写英文字母、数字和连字符，例如 team-notices。') ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'maxlength' => 255]) ?>
    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => -32768, 'max' => 32767]) ?>
    <?= $form->field($model, 'is_active')->dropDownList(['1' => '启用', '0' => '停用']) ?>
    <p><?= Html::submitButton('保存分类', ['class' => 'btn btn-success']) ?>
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-outline-secondary']) ?></p>
    <?php ActiveForm::end(); ?>
</section>
