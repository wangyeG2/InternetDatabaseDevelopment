<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\models;

use common\models\TeamLink;

class TeamLinkForm extends TeamForm
{
    public const RECORD_CLASS = TeamLink::class;
    public string $label = '';
    public string $url = '';
    public string $sort_order = '0';
    public string $is_public = '0';

    public function rules(): array
    {
        return [[['label', 'url', 'sort_order', 'is_public'], 'safe']];
    }

    public static function forRecord(TeamLink $record): self
    {
        $form = new self();
        $form->record = $record;
        foreach (['label', 'url', 'sort_order', 'is_public'] as $field) {
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
