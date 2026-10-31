<?php

/** Parameterized, paginated news search for public and management lists. Maintainer: B. */
declare(strict_types=1);

namespace common\models;

use yii\data\ActiveDataProvider;

class NewsSearch extends RequestForm
{
    public string $q = '';
    public string $category = '';
    public string $tag = '';
    public string $status = '';

    public function rules(): array
    {
        return [
            ['q', 'trim'],
            ['q', 'string', 'max' => 120],
            [['category', 'tag'], 'integer', 'min' => 1],
            ['status', 'in', 'range' => ['0', '10', '20']],
        ];
    }

    public function search(array $params, bool $management = false): ActiveDataProvider
    {
        $this->load($params, '');
        $query = News::find()->with(['category', 'author', 'tags'])->andWhere(['n.deleted_at' => null]);
        if (!$management) {
            $query->publiclyVisible();
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $management ? 20 : 12, 'pageSizeLimit' => [1, 50]],
            'sort' => [
                'attributes' => ['title', 'created_at', 'updated_at', 'published_at', 'id', 'is_pinned'],
                'defaultOrder' => $management ? ['updated_at' => SORT_DESC, 'id' => SORT_DESC]
                    : ['is_pinned' => SORT_DESC, 'published_at' => SORT_DESC, 'id' => SORT_DESC],
            ],
        ]);
        if (!$this->validate()) {
            $query->andWhere('0=1');
            return $provider;
        }
        $query->andFilterWhere(['n.category_id' => $this->category]);
        if ($management && $this->status !== '') {
            $query->andWhere(['n.status' => (int) $this->status]);
        }
        if ($this->q !== '') {
            $query->andWhere(['or', ['like', 'n.title', $this->q], ['like', 'n.summary', $this->q]]);
        }
        if ($this->tag !== '') {
            $query->andWhere(['n.id' => (new \yii\db\Query())->select('news_id')
                ->from('{{%news_tag_link}}')->where(['tag_id' => (int) $this->tag])]);
        }
        return $provider;
    }

    public function attributeLabels(): array
    {
        return ['q' => '关键词', 'category' => '分类', 'tag' => '标签', 'status' => '状态'];
    }
}
