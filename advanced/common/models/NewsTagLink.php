<?php

/** Composite-key relation between news and tags. Maintainer: B. */

declare(strict_types=1);

namespace common\models;

use yii\db\ActiveRecord;

class NewsTagLink extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%news_tag_link}}';
    }
}
