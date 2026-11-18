<?php

/** Administrator account list. Variables: search, dataProvider. Maintainer: A. */

declare(strict_types=1);

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\UserSearch $search */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
$statusLabels = [
    User::STATUS_DELETED => '删除状态',
    User::STATUS_INACTIVE => '待验证',
    User::STATUS_ACTIVE => '启用',
];
?>
<section class="user-admin">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>可调整账号角色和状态；为保留新闻、团队资料等关联记录，此处不提供物理删除。</p>

    <form action="<?= Html::encode(Url::to(['index'])) ?>" method="get" class="row g-3 mb-4">
        <?= Html::hiddenInput('r', 'user/index') ?>
        <div class="col-md-3">
            <?= Html::activeLabel($search, 'username', ['class' => 'form-label']) ?>
            <?= Html::activeTextInput($search, 'username', ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::activeLabel($search, 'email', ['class' => 'form-label']) ?>
            <?= Html::activeTextInput($search, 'email', ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::activeLabel($search, 'role', ['class' => 'form-label']) ?>
            <?= Html::activeDropDownList($search, 'role', [
                '' => '全部',
                User::ROLE_MEMBER => '普通成员',
                User::ROLE_ADMIN => '管理员',
            ], ['class' => 'form-select']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::activeLabel($search, 'status', ['class' => 'form-label']) ?>
            <?= Html::activeDropDownList($search, 'status', [
                '' => '全部',
                User::STATUS_ACTIVE => '启用',
                User::STATUS_INACTIVE => '待验证',
                User::STATUS_DELETED => '删除状态',
            ], ['class' => 'form-select']) ?>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary w-100']) ?>
        </div>
    </form>

    <?= Html::errorSummary($search, ['class' => 'alert alert-danger']) ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
            'emptyText' => '没有匹配的用户。',
            'columns' => [
                ['attribute' => 'id', 'label' => '编号'],
                ['attribute' => 'username', 'label' => '用户名'],
                ['attribute' => 'email', 'label' => '邮箱'],
                [
                    'attribute' => 'role',
                    'label' => '角色',
                    'value' => static fn (User $user): string =>
                        $user->role === User::ROLE_ADMIN ? '管理员' : '普通成员',
                ],
                [
                    'attribute' => 'status',
                    'label' => '状态',
                    'value' => static fn (User $user): string => $statusLabels[(int) $user->status] ?? '未知',
                ],
                ['attribute' => 'created_at', 'label' => '注册时间', 'format' => ['datetime', 'php:Y-m-d H:i']],
                [
                    'class' => yii\grid\ActionColumn::class,
                    'template' => '{update}',
                    'buttons' => [
                        'update' => static fn ($url, User $user): string => Html::a('管理', $url, [
                            'aria-label' => '管理用户 ' . $user->username,
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</section>
