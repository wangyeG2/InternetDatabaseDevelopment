<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\services;

use backend\models\TeamForm;
use backend\models\TeamMemberForm;
use common\models\TeamMember;
use common\models\TeamMemberDuty;
use common\models\TeamProfile;
use common\models\User;
use DomainException;
use RuntimeException;
use Yii;

class TeamService
{
    public function save(TeamForm $form): bool
    {
        $user = Yii::$app->user->identity;
        if (!$user instanceof User || !$user->isAdmin()) {
            throw new DomainException('仅激活管理员可编辑团队资料。');
        }
        if (!$form->validate()) {
            return false;
        }
        $record = $form->toRecord();
        if ($record instanceof TeamProfile) {
            $record->is_current = 1;
            $record->updated_by = $user->id;
        }
        $duties = [];
        if ($record instanceof TeamMember && $form instanceof TeamMemberForm) {
            if (mb_strlen($form->duties) > 20000) {
                $form->addError('duties', '职责内容最多 20000 字符。');
                return false;
            }
            foreach (preg_split('/\R/u', trim($form->duties), -1, PREG_SPLIT_NO_EMPTY) as $index => $line) {
                $parts = array_map('trim', explode('|', $line, 2));
                $duty = new TeamMemberDuty(['title' => $parts[0], 'detail' => $parts[1] ?? '', 'sort_order' => $index]);
                if (!$duty->validate(['title', 'detail', 'sort_order'])) {
                    $form->addError('duties', '每行格式：标题 | 说明；标题最多120字，说明最多1000字。');
                }
                $duties[] = $duty;
            }
            if (count($duties) > 30) {
                $form->addError('duties', '最多30行职责。');
            }
        }
        if (!$record->validate()) {
            $form->addErrors($record->getErrors());
        }
        if ($form->hasErrors()) {
            return false;
        }
        Yii::$app->db->transaction(static function () use ($record, $duties): void {
            if (!$record->save(false)) {
                throw new RuntimeException('团队数据保存失败。');
            }
            if ($record instanceof TeamMember) {
                TeamMemberDuty::deleteAll(['member_id' => $record->id]);
                foreach ($duties as $duty) {
                    $duty->member_id = $record->id;
                    if (!$duty->save()) {
                        throw new RuntimeException('职责保存失败。');
                    }
                }
            }
        });
        return true;
    }
}
