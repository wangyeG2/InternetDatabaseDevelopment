<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\models;

use common\models\TeamMember;

class TeamMemberForm extends TeamForm
{
    public const RECORD_CLASS = TeamMember::class;
    public string $name = '';
    public string $slug = '';
    public string $module_code = '';
    public string $role_title = '';
    public string $duty = '';
    public string $bio = '';
    public string $sort_order = '0';
    public string $is_public = '0';
    public string $duties = '';

    public function rules(): array
    {
        return [[['name', 'slug', 'module_code', 'role_title', 'duty', 'bio', 'sort_order', 'is_public', 'duties'], 'safe']];
    }

    public static function forRecord(TeamMember $record): self
    {
        $form = new self();
        $form->record = $record;
        foreach (['name', 'slug', 'module_code', 'role_title', 'duty', 'bio', 'sort_order', 'is_public'] as $field) {
            $form->$field = (string) $record->$field;
        }
        $form->duties = implode("\n", array_map(static fn ($duty) => $duty->title . ' | ' . $duty->detail, $record->duties));
        return $form;
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        return (new \backend\services\TeamService())->save($this);
    }
}
