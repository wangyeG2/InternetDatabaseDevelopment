<?php

/** Message validation, visibility and moderation. Maintainer: D (秦东杰). */

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string $topic
 * @property string $body
 * @property int $status
 * @property int $created_at
 * @property int|null $moderated_by
 * @property int|null $moderated_at
 * @property-read User|null $author
 */
final class Message extends ActiveRecord
{
    public const PENDING = 0;
    public const PUBLISHED = 1;
    public const HIDDEN = 20;

    public const TOPICS = [
        'matches' => '赛事讨论',
        'team' => '团队交流',
        'feedback' => '网站建议',
    ];

    public const STATUSES = [
        'pending' => self::PENDING,
        'published' => self::PUBLISHED,
        'hidden' => self::HIDDEN,
    ];

    public const STATUS_LABELS = [
        self::PENDING => '待审核',
        self::PUBLISHED => '已公开',
        self::HIDDEN => '已隐藏',
    ];

    public const MIN_LENGTH = 5;
    public const MAX_LENGTH = 500;

    /** Honeypot; accept any request value here so validation can reject malformed input. */
    public mixed $website = '';

    public static function tableName(): string
    {
        return '{{%message}}';
    }

    public function behaviors(): array
    {
        return [['class' => TimestampBehavior::class, 'updatedAtAttribute' => false]];
    }

    public function rules(): array
    {
        return [
            [['topic', 'body'], 'validateTextInput', 'skipOnEmpty' => false],
            ['body', 'trim'],
            ['website', 'validateHoneypot', 'skipOnEmpty' => false],
            [['topic', 'body'], 'required', 'message' => '请填写{attribute}。'],
            ['topic', 'in', 'range' => array_keys(self::TOPICS), 'message' => '请选择有效的话题。'],
            ['body', 'string', 'min' => self::MIN_LENGTH, 'max' => self::MAX_LENGTH,
                'message' => '请输入文字留言。', 'tooShort' => '留言至少需要 {min} 个字符。',
                'tooLong' => '留言最多允许 {max} 个字符。', 'enableClientValidation' => false],
        ];
    }

    public function validateHoneypot(string $attribute): void
    {
        if ($this->website !== '') {
            $this->addError($attribute, '表单校验失败。');
        }
    }

    public function validateTextInput(string $attribute): void
    {
        if ($this->$attribute !== null && !is_string($this->$attribute)) {
            $this->$attribute = '';
            $this->addError($attribute, '请为' . $this->getAttributeLabel($attribute) . '提供单个文本值。');
        }
    }

    public function attributeLabels(): array
    {
        return ['topic' => '话题', 'body' => '留言'];
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public static function published(): ActiveQuery
    {
        return self::find()->andWhere(['status' => self::PUBLISHED]);
    }

    /** @return array<string, int> */
    public static function publishedTopicCounts(): array
    {
        $counts = array_fill_keys(array_keys(self::TOPICS), 0);
        $rows = self::published()
            ->select(['topic', 'total' => new Expression('COUNT(*)')])
            ->groupBy('topic')
            ->asArray()
            ->all();

        foreach ($rows as $row) {
            if (isset($counts[$row['topic']])) {
                $counts[$row['topic']] = (int) $row['total'];
            }
        }

        return $counts;
    }

    public function submit(int $authorId): bool
    {
        $this->author_id = $authorId;
        $this->status = self::PENDING;
        return $this->save();
    }

    public function moderate(int $status, int $moderatorId, int $expectedStatus): bool
    {
        return self::updateAll(
            ['status' => $status, 'moderated_by' => $moderatorId, 'moderated_at' => time()],
            ['id' => $this->id, 'status' => $expectedStatus],
        ) === 1;
    }
}
