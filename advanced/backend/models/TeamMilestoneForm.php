<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\models;

use common\models\TeamMilestone;

class TeamMilestoneForm extends TeamForm
{
    public const RECORD_CLASS = TeamMilestone::class;
    public string $title = '';
    public string $happened_on = '';
    public string $summary = '';
    public string $sort_order = '0';
    public string $is_public = '0';
    public string $is_example = '0';

    public function rules(): array
    {
        return [[['title', 'happened_on', 'summary', 'sort_order', 'is_public', 'is_example'], 'safe']];
    }

    public static function forRecord(TeamMilestone $record): self
    {
        $form = new self();
        $form->record = $record;
        foreach (['title', 'happened_on', 'summary', 'sort_order', 'is_public', 'is_example'] as $field) {
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
