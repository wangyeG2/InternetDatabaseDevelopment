<?php

/** 留言校验表单。维护：秦东杰。 */

declare(strict_types=1);

use common\models\Message;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>
<?php $form = ActiveForm::begin(['id' => 'message-form', 'action' => ['create'], 'enableAjaxValidation' => true, 'validationUrl' => ['create']]); ?>
<?= $form->errorSummary($model) ?>
<div hidden aria-hidden="true"><?= Html::activeTextInput($model, 'website', ['tabindex' => -1, 'autocomplete' => 'off']) ?></div>
<?= $form->field($model, 'topic')->dropDownList(Message::TOPICS, ['prompt' => '选择留言话题']) ?>
<?= $form->field($model, 'body')->textarea([
    'rows' => 4,
    'placeholder' => '分享你的看法或建议',
    'data-message-min' => Message::MIN_LENGTH,
    'data-message-max' => Message::MAX_LENGTH,
    'aria-describedby' => 'message-length',
    'data-message-body' => '',
]) ?>
<p id="message-length" class="message-helper">
    <?= Message::MIN_LENGTH ?>–<?= Message::MAX_LENGTH ?> 字符 · 审核后公开
    <span data-message-length></span>
</p>
<?= Html::submitButton('提交审核', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
