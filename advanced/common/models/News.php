<?php

/** Shared news record and relationships for both Yii applications. Maintainer: B. */

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $category_id
 * @property int $author_id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string $content
 * @property int $status
 * @property int $is_pinned
 * @property int|null $published_at
 * @property int|null $deleted_at
 * @property int $created_at
 * @property int $updated_at
 * @property NewsCategory $category
 * @property User $author
 * @property NewsTag[] $tags
 * @property NewsRevision[] $revisions
 * @property string $statusLabel
 */
class News extends ActiveRecord
{
    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 10;
    public const STATUS_ARCHIVED = 20;

    public static function tableName(): string
    {
        return '{{%news}}';
    }

    public static function find(): NewsQuery
    {
        return new NewsQuery(static::class);
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['title', 'slug', 'content', 'category_id', 'author_id'], 'required'],
            ['title', 'string', 'max' => 180],
            ['slug', 'string', 'max' => 96],
            ['slug', 'unique'],
            ['summary', 'string', 'max' => 360],
            ['content', 'string', 'max' => 50000],
            [['category_id', 'author_id'], 'integer', 'min' => 1],
            ['category_id', 'exist', 'targetClass' => NewsCategory::class, 'targetAttribute' => 'id'],
            ['author_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            ['status', 'in', 'range' => array_keys(self::statusOptions())],
            ['is_pinned', 'boolean'],
        ];
    }

    public static function statusOptions(): array
    {
        return [self::STATUS_DRAFT => '草稿', self::STATUS_PUBLISHED => '已发布', self::STATUS_ARCHIVED => '已归档'];
    }

    public function getStatusLabel(): string
    {
        return $this->deleted_at !== null ? '已删除' : (self::statusOptions()[$this->status] ?? '未知');
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(NewsCategory::class, ['id' => 'category_id']);
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(NewsTag::class, ['id' => 'tag_id'])
            ->viaTable('{{%news_tag_link}}', ['news_id' => 'id'])->orderBy(['name' => SORT_ASC]);
    }

    public function getRevisions(): ActiveQuery
    {
        return $this->hasMany(NewsRevision::class, ['news_id' => 'id'])
            ->with('editor')->orderBy(['id' => SORT_DESC]);
    }

    public function getTagNames(): array
    {
        return array_map(static fn (NewsTag $tag): string => (string) $tag->name, $this->tags);
    }
}
