<?php

/** News editor view. Variables: model (NewsForm), categories. Maintainer: B. */
declare(strict_types=1);

use common\models\News;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin(['id' => 'news-form']);
?>
<?= $form->errorSummary($model) ?>
<?= $form->field($model, 'title')->textInput(['maxlength' => 180]) ?>
<div class="row">
    <div class="col-md-6"><?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '请选择分类']) ?></div>
    <div class="col-md-3"><?= $form->field($model, 'status')->dropDownList(News::statusOptions()) ?></div>
    <div class="col-md-3"><?= $form->field($model, 'is_pinned')->dropDownList(['0' => '普通新闻', '1' => '置顶新闻']) ?></div>
</div>
<?= $form->field($model, 'summary')->textarea(['rows' => 3, 'maxlength' => 360]) ?>
<?= $form->field($model, 'content')->textarea(['rows' => 14, 'maxlength' => 50000])->hint('正文按纯文本保存，换行会保留。') ?>
<?= $form->field($model, 'tags')->textInput(['maxlength' => 400])->hint('用逗号分隔，最多 10 个标签，每个最多 32 个字符。') ?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => 96])->hint('可留空自动生成；自定义时只使用小写英文字母、数字和连字符。') ?>
<p><?= Html::submitButton('保存新闻', ['class' => 'btn btn-success', 'id' => 'save-news']) ?>
    <?= Html::a('取消', ['index'], ['class' => 'btn btn-outline-secondary']) ?></p>
<?php ActiveForm::end(); ?>
