<?php

/** News category model and relation contract. Maintainer: B. */
declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/** @property int $id @property string $name @property string $slug @property int $is_active */
class NewsCategory extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%news_category}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            ['name', 'string', 'max' => 60],
            ['slug', 'string', 'max' => 80],
            ['slug', 'match', 'pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D'],
            [['name', 'slug'], 'unique'],
            ['description', 'string', 'max' => 255],
            ['sort_order', 'integer', 'min' => -32768, 'max' => 32767],
            ['is_active', 'boolean'],
        ];
    }

    public function getNews(): ActiveQuery
    {
        return $this->hasMany(News::class, ['category_id' => 'id']);
    }

    public static function options(bool $activeOnly = true): array
    {
        $query = static::find()->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
        if ($activeOnly) {
            $query->andWhere(['is_active' => 1]);
        }
        return $query->select('name')->indexBy('id')->column();
    }
}
