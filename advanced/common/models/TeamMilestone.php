<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string $title
 * @property string $happened_on
 * @property string $summary
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $sort_order
 * @property int $is_public
 * @property int $is_example
 */
class TeamMilestone extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%team_milestone}}';
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
            [['sort_order', 'is_public', 'is_example'], 'required'],
            [['title', 'happened_on'], 'required'],
            ['title', 'string', 'max' => 180],
            ['happened_on', 'string', 'max' => 10],
            ['summary', 'string', 'max' => 1000],
            ['happened_on', 'date', 'format' => 'php:Y-m-d'],
            ['is_example', 'in', 'range' => [0, 1, '0', '1'], 'strict' => true],
            ['sort_order', 'integer', 'min' => -32768, 'max' => 32767],
            ['is_public', 'in', 'range' => [0, 1, '0', '1'], 'strict' => true],
        ];
    }
}
