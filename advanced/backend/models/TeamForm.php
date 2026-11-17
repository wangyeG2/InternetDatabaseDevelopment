<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\models;

use common\models\RequestForm;
use common\models\TeamMemberDuty;
use yii\db\ActiveRecord;

abstract class TeamForm extends RequestForm
{
    public const RECORD_CLASS = ActiveRecord::class;
    public ?ActiveRecord $record = null;

    public function toRecord(): ActiveRecord
    {
        $class = static::RECORD_CLASS;
        $record = $this->record === null ? new $class() : clone $this->record;
        foreach ($this->safeAttributes() as $field) {
            if ($field !== 'duties') {
                $record->$field = trim($this->$field);
            }
        }
        return $record;
    }

    public function afterValidate(): void
    {
        parent::afterValidate();
        if ($this->hasErrors()) {
            return;
        }
        $record = $this->toRecord();
        if (!$record->validate()) {
            $this->addErrors($record->getErrors());
        }
        if ($this instanceof TeamMemberForm) {
            if (mb_strlen($this->duties) > 20000) {
                $this->addError('duties', '职责最多20000字。');
                return;
            }
            $lines = preg_split('/\R/u', trim($this->duties), -1, PREG_SPLIT_NO_EMPTY);
            if ($lines === false || count($lines) > 30) {
                $this->addError('duties', '职责最多30行、20000字。');
                return;
            }
            foreach ($lines as $line) {
                $parts = array_map('trim', explode('|', $line, 2));
                $duty = new TeamMemberDuty(['title' => $parts[0], 'detail' => $parts[1] ?? '']);
                if (!$duty->validate(['title', 'detail'])) {
                    $this->addError('duties', '每行格式：标题 | 说明；标题最多120字，说明最多1000字。');
                }
            }
        }
    }
}
