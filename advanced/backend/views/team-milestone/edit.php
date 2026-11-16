<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\TeamMilestoneForm $model */
$this->title = '项目时间线编辑';
\common\assets\TeamAsset::register($this);
?>
<section class="team-editor">
<h1><?= Html::encode($this->title) ?></h1>
<p>资料以纯文本保存；隐藏后仍可在后台编辑。</p>
<?php $form = ActiveForm::begin(['id' => 'team-form']); ?>
<?= $form->errorSummary($model, ['class' => 'error-summary']) ?>
<?= $form->field($model, 'title')->textInput()->label('标题') ?>
<?= $form->field($model, 'happened_on')->textInput()->label('日期（YYYY-MM-DD）') ?>
<?= $form->field($model, 'summary')->textarea(['rows' => 6])->label('摘要') ?>
<?= $form->field($model, 'sort_order')->textInput()->label('显示顺序') ?>
<?= $form->field($model, 'is_public')->dropDownList(['0' => '否', '1' => '是'])->label('公开') ?>
<?= $form->field($model, 'is_example')->dropDownList(['0' => '否', '1' => '是'])->label('示例记录') ?>
<?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>
</section>
