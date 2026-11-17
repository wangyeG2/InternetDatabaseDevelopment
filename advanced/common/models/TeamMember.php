<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string $name
 * @property string $slug
 * @property string $module_code
 * @property string $role_title
 * @property string $duty
 * @property string $bio
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $sort_order
 * @property int $is_public
 * @property int|null $user_id
 * @property TeamMemberDuty[] $duties
 */
class TeamMember extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%team_member}}';
    }

    public static function find(): TeamQuery
    {
        return new TeamQuery(static::class);
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['sort_order', 'is_public'], 'required'],
            [['name', 'slug'], 'required'],
            ['name', 'string', 'max' => 60],
            ['slug', 'string', 'max' => 80],
            ['module_code', 'string', 'max' => 1],
            ['role_title', 'string', 'max' => 120],
            ['duty', 'string', 'max' => 255],
            ['bio', 'string', 'max' => 12000],
            ['slug', 'match', 'pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D'],
            ['slug', 'unique'],
            ['module_code', 'in', 'range' => ['', 'A', 'B', 'C', 'D']],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            ['sort_order', 'integer', 'min' => -32768, 'max' => 32767],
            ['is_public', 'in', 'range' => [0, 1, '0', '1'], 'strict' => true],
        ];
    }

    public function getDuties(): \yii\db\ActiveQuery
    {
        return $this->hasMany(TeamMemberDuty::class, ['member_id' => 'id'])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
