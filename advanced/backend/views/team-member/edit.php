<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\TeamMemberForm $model */
$this->title = '成员管理编辑';
\common\assets\TeamAsset::register($this);
?>
<section class="team-editor">
<h1><?= Html::encode($this->title) ?></h1>
<p>资料以纯文本保存；隐藏后仍可在后台编辑。</p>
<?php $form = ActiveForm::begin(['id' => 'team-form']); ?>
<?= $form->errorSummary($model, ['class' => 'error-summary']) ?>
<?= $form->field($model, 'name')->textInput()->label('名称') ?>
<?= $form->field($model, 'slug')->textInput()->label('标识（小写字母、数字、连字符）') ?>
<?= $form->field($model, 'module_code')->dropDownList(['' => '未分配', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'])->label('模块代码') ?>
<?= $form->field($model, 'role_title')->textInput()->label('角色') ?>
<?= $form->field($model, 'duty')->textInput()->label('主要职责') ?>
<?= $form->field($model, 'bio')->textarea(['rows' => 6])->label('个人介绍（纯文本）') ?>
<?= $form->field($model, 'sort_order')->textInput()->label('显示顺序') ?>
<?= $form->field($model, 'is_public')->dropDownList(['0' => '否', '1' => '是'])->label('公开') ?>
<?= $form->field($model, 'duties')->textarea(['rows' => 6])->label('额外职责（每行：标题 | 说明）') ?>
<?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>
</section>
