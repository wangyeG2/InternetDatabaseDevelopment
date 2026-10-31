<?php

/** Category editor form with safe request assignment. Maintainer: B. */
declare(strict_types=1);

namespace backend\models;

use common\models\NewsCategory;
use common\models\RequestForm;

class NewsCategoryForm extends RequestForm
{
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $sort_order = '0';
    public string $is_active = '1';
    public ?NewsCategory $record = null;

    public function rules(): array
    {
        return [
            [['name', 'slug', 'description'], 'trim'],
            [['name', 'slug'], 'required'],
            ['name', 'string', 'max' => 60],
            ['slug', 'string', 'max' => 80],
            ['slug', 'match', 'pattern' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/D'],
            ['description', 'string', 'max' => 255],
            ['sort_order', 'integer', 'min' => -32768, 'max' => 32767],
            ['is_active', 'in', 'range' => ['0', '1']],
            [['name', 'slug'], 'unique', 'targetClass' => NewsCategory::class,
                'filter' => fn ($query) => $query->andWhere(['not', ['id' => $this->record?->id]])],
        ];
    }

    public static function forRecord(NewsCategory $category): self
    {
        $form = new self();
        $form->record = $category;
        foreach (['name', 'slug', 'description', 'sort_order', 'is_active'] as $field) {
            $form->$field = (string) $category->$field;
        }
        return $form;
    }

    public function save(): ?NewsCategory
    {
        if (!$this->validate()) {
            return null;
        }
        $record = $this->record ?? new NewsCategory();
        foreach (['name', 'slug', 'description'] as $field) {
            $record->$field = $this->$field;
        }
        $record->sort_order = (int) $this->sort_order;
        $record->is_active = (int) $this->is_active;
        if (!$record->save()) {
            $this->addErrors($record->getErrors());
            return null;
        }
        return $record;
    }

    public function attributeLabels(): array
    {
        return ['name' => '分类名称', 'slug' => '分类标识', 'description' => '说明', 'sort_order' => '显示顺序', 'is_active' => '启用'];
    }
}
