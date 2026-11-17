<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\models;

use common\models\TeamProfile;

class TeamProfileForm extends TeamForm
{
    public const RECORD_CLASS = TeamProfile::class;
    public string $name = '';
    public string $slug = '';
    public string $motto = '';
    public string $summary = '';
    public string $body = '';
    public string $theme_note = '';

    public function rules(): array
    {
        return [[['name', 'slug', 'motto', 'summary', 'body', 'theme_note'], 'safe']];
    }

    public static function forRecord(TeamProfile $record): self
    {
        $form = new self();
        $form->record = $record;
        foreach (['name', 'slug', 'motto', 'summary', 'body', 'theme_note'] as $field) {
            $form->$field = (string) $record->$field;
        }
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
