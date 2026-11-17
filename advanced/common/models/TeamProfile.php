<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string $name
 * @property string $slug
 * @property string $motto
 * @property string $summary
 * @property string $body
 * @property string $theme_note
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $is_current
 * @property int|null $updated_by
 */
class TeamProfile extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%team_profile}}';
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
            [['name', 'slug'], 'required'],
            ['name', 'string', 'max' => 120],
            ['slug', 'string', 'max' => 80],
            ['motto', 'string', 'max' => 180],
            ['summary', 'string', 'max' => 500],
            ['body', 'string', 'max' => 12000],
            ['theme_note', 'string', 'max' => 500],
            ['slug', 'match', 'pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D'],
            ['slug', 'unique'],
            ['is_current', 'in', 'range' => [1, '1'], 'skipOnEmpty' => true],
            ['updated_by', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
        ];
    }

    public static function current(): ?self
    {
        $profile = static::find()->where(['is_current' => 1])->one();
        return $profile instanceof self ? $profile : null;
    }
}
