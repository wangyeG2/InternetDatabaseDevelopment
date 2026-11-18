<?php

/** Account role and status form. Variable: model. Maintainer: A. */

declare(strict_types=1);

use common\models\User;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserManagementForm $model */

$form = ActiveForm::begin(['id' => 'user-form']);
?>
<?= $form->errorSummary($model, ['class' => 'error-summary']) ?>
<?= $form->field($model, 'role')->dropDownList([
    User::ROLE_MEMBER => '普通成员',
    User::ROLE_ADMIN => '管理员',
]) ?>
<?= $form->field($model, 'status')->dropDownList([
    User::STATUS_ACTIVE => '启用',
    User::STATUS_INACTIVE => '待验证',
    User::STATUS_DELETED => '删除状态（不可登录）',
])->hint('账号状态变更不会物理删除用户记录，以保留其关联内容。') ?>
<p>
    <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    <?= Html::a('返回', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</p>
<?php ActiveForm::end(); ?>
