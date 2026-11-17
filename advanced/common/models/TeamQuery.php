<?php

/** Public visibility helper for team roster, milestones and links. Maintainer: C. */

declare(strict_types=1);

namespace common\models;

use yii\base\InvalidCallException;
use yii\db\ActiveQuery;

class TeamQuery extends ActiveQuery
{
    public function publiclyVisible(): self
    {
        $model = new $this->modelClass();
        if (!$model->hasAttribute('is_public')) {
            throw new InvalidCallException('publiclyVisible() 仅用于含 is_public 字段的团队记录。');
        }
        return $this->andWhere(['is_public' => 1]);
    }

    public function ordered(): self
    {
        return $this->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
