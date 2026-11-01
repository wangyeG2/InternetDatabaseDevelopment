<?php

/** Immutable news edit and publication history. Maintainer: B. */

declare(strict_types=1);

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $news_id
 * @property int $editor_id
 * @property string $operation
 * @property string $snapshot
 * @property int $created_at
 * @property User $editor
 * @property string $operationLabel
 */
class NewsRevision extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%news_revision}}';
    }

    public function getEditor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'editor_id']);
    }

    public function getOperationLabel(): string
    {
        return ['create' => '创建', 'update' => '编辑', 'publish' => '发布', 'withdraw' => '撤回', 'delete' => '删除'][$this->operation]
            ?? (string) $this->operation;
    }
}
