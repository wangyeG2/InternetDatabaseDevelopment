<?php

/** Shared public visibility rule; used by list and detail routes. Maintainer: B. */
declare(strict_types=1);

namespace common\models;

use yii\db\ActiveQuery;

class NewsQuery extends ActiveQuery
{
    public function init(): void
    {
        parent::init();
        $this->alias('n');
    }

    public function publiclyVisible(): self
    {
        return $this->joinWith(['category c'], false)
            ->andWhere(['n.status' => News::STATUS_PUBLISHED, 'n.deleted_at' => null, 'c.is_active' => 1])
            ->andWhere(['<=', 'n.published_at', time()]);
    }
}
