<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string $label
 * @property string $url
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int $sort_order
 * @property int $is_public
 */
class TeamLink extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%team_link}}';
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
            [['label', 'url'], 'required'],
            ['label', 'string', 'max' => 120],
            ['url', 'string', 'max' => 2048],
            ['url', 'url', 'validSchemes' => ['http', 'https']],
            ['sort_order', 'integer', 'min' => -32768, 'max' => 32767],
            ['is_public', 'in', 'range' => [0, 1, '0', '1'], 'strict' => true],
        ];
    }
}
