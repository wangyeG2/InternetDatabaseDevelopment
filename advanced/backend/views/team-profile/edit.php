<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\TeamProfileForm $model */
$this->title = '团队资料编辑';
\common\assets\TeamAsset::register($this);
?>
<section class="team-editor">
<h1><?= Html::encode($this->title) ?></h1>
<p>资料以纯文本保存；隐藏后仍可在后台编辑。</p>
<?php $form = ActiveForm::begin(['id' => 'team-form']); ?>
<?= $form->errorSummary($model, ['class' => 'error-summary']) ?>
<?= $form->field($model, 'name')->textInput()->label('名称') ?>
<?= $form->field($model, 'slug')->textInput()->label('标识（小写字母、数字、连字符）') ?>
<?= $form->field($model, 'motto')->textInput()->label('口号') ?>
<?= $form->field($model, 'summary')->textarea(['rows' => 6])->label('摘要') ?>
<?= $form->field($model, 'body')->textarea(['rows' => 6])->label('团队正文（纯文本）') ?>
<?= $form->field($model, 'theme_note')->textarea(['rows' => 6])->label('世界杯主题说明') ?>
<?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>
</section>
