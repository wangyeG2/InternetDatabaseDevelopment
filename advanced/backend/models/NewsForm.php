<?php

/** Explicit news editor input contract; author and timestamps are server-owned. Maintainer: B. */
declare(strict_types=1);

namespace backend\models;

use common\models\News;
use common\models\NewsCategory;
use common\models\RequestForm;

class NewsForm extends RequestForm
{
    public string $title = '';
    public string $slug = '';
    public string $summary = '';
    public string $content = '';
    public string $category_id = '';
    public string $status = '0';
    public string $is_pinned = '0';
    public string $tags = '';
    public ?News $record = null;

    public function rules(): array
    {
        return [
            [['title', 'slug', 'summary', 'content', 'tags'], 'trim'],
            [['title', 'content', 'category_id'], 'required'],
            ['title', 'string', 'max' => 180],
            ['summary', 'string', 'max' => 360],
            ['content', 'string', 'max' => 50000],
            ['slug', 'string', 'max' => 96],
            ['slug', 'match', 'pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D'],
            ['slug', 'unique', 'targetClass' => News::class, 'targetAttribute' => 'slug',
                'filter' => fn ($query) => $query->andWhere(['not', ['id' => $this->record?->id]])],
            ['category_id', 'integer', 'min' => 1],
            ['category_id', 'exist', 'targetClass' => NewsCategory::class, 'targetAttribute' => 'id'],
            ['status', 'in', 'range' => ['0', '10', '20']],
            ['is_pinned', 'in', 'range' => ['0', '1']],
            ['tags', 'string', 'max' => 400],
            ['tags', 'validateTags'],
        ];
    }

    public function validateTags(string $attribute): void
    {
        $tags = $this->tagNames();
        if (count($tags) > 10) {
            $this->addError($attribute, '最多填写 10 个标签。');
        }
        foreach ($tags as $tag) {
            if (mb_strlen($tag, 'UTF-8') > 32) {
                $this->addError($attribute, '每个标签最多 32 个字符。');
                break;
            }
        }
    }

    public function tagNames(): array
    {
        $values = preg_split('/[,，]/u', $this->tags) ?: [];
        return array_values(array_unique(array_filter(array_map(
            static fn (string $value): string => mb_strtolower(trim($value), 'UTF-8'), $values,
        ), static fn (string $value): bool => $value !== '')));
    }

    public static function forRecord(News $news): self
    {
        $form = new self();
        $form->record = $news;
        foreach (['title', 'slug', 'summary', 'content', 'category_id', 'status', 'is_pinned'] as $field) {
            $form->$field = (string) $news->$field;
        }
        $form->tags = implode(', ', $news->getTagNames());
        return $form;
    }

    public function attributeLabels(): array
    {
        return ['title' => '标题', 'slug' => '标识', 'summary' => '摘要', 'content' => '正文',
            'category_id' => '分类', 'status' => '状态', 'is_pinned' => '置顶', 'tags' => '标签'];
    }
}
