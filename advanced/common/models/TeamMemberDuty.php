<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property string $title
 * @property string $detail
 * @property int $id
 * @property int $sort_order
 * @property int $member_id
 */
class TeamMemberDuty extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%team_member_duty}}';
    }

    public static function find(): TeamQuery
    {
        return new TeamQuery(static::class);
    }

    public function behaviors(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [
            [['sort_order'], 'required'],
            [['title', 'member_id'], 'required'],
            ['title', 'string', 'max' => 120],
            ['detail', 'string', 'max' => 1000],
            ['sort_order', 'integer', 'min' => -32768, 'max' => 32767],
            ['member_id', 'exist', 'targetClass' => TeamMember::class, 'targetAttribute' => 'id'],
        ];
    }
}
