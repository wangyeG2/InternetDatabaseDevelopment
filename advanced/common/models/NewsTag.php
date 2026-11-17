<?php

/** Normalized news tag model. Maintainer: B. */

declare(strict_types=1);

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property int $created_at
 */
class NewsTag extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%news_tag}}';
    }

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 32],
            ['name', 'unique'],
            ['created_at', 'integer'],
        ];
    }
}
