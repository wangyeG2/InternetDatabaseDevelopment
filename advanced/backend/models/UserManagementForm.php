<?php

/** Validated account role and status changes. Maintainer: A. */

declare(strict_types=1);

namespace backend\models;

use common\models\RequestForm;
use common\models\User;
use Yii;

final class UserManagementForm extends RequestForm
{
    public string $role = '';
    public string $status = '';
    public ?User $record = null;

    public function rules(): array
    {
        return [
            [['role', 'status'], 'required'],
            ['role', 'in', 'range' => [User::ROLE_MEMBER, User::ROLE_ADMIN]],
            ['status', 'in', 'range' => [
                (string) User::STATUS_DELETED,
                (string) User::STATUS_INACTIVE,
                (string) User::STATUS_ACTIVE,
            ]],
        ];
    }

    public static function forRecord(User $record): self
    {
        $form = new self();
        $form->record = $record;
        $form->role = $record->role;
        $form->status = (string) $record->status;

        return $form;
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->record === null) {
            $this->addError('role', '缺少要编辑的用户记录。');
            return false;
        }

        $willRemainActiveAdmin = $this->role === User::ROLE_ADMIN
            && (int) $this->status === User::STATUS_ACTIVE;
        $isCurrentUser = (int) Yii::$app->user->id === (int) $this->record->id;

        if ($isCurrentUser && !$willRemainActiveAdmin) {
            $this->addError('role', '不能停用或移除当前登录管理员的管理员权限。');
            return false;
        }

        $wasActiveAdmin = $this->record->role === User::ROLE_ADMIN
            && (int) $this->record->status === User::STATUS_ACTIVE;
        if ($wasActiveAdmin && !$willRemainActiveAdmin) {
            $otherActiveAdminExists = User::find()
                ->where(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE])
                ->andWhere(['<>', 'id', $this->record->id])
                ->exists();
            if (!$otherActiveAdminExists) {
                $this->addError('role', '系统必须至少保留一名启用的管理员。');
                return false;
            }
        }

        $this->record->role = $this->role;
        $this->record->status = (int) $this->status;
        if (!$this->record->save()) {
            $this->addErrors($this->record->getErrors());
            return false;
        }

        return true;
    }

    public function attributeLabels(): array
    {
        return [
            'role' => '用户角色',
            'status' => '账号状态',
        ];
    }
}
