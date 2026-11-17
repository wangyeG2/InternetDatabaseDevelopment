<?php

/**
 * Backend-wide management access guard. Maintainer: B.
 * All current and future backend controllers use the same role boundary.
 */

declare(strict_types=1);

namespace backend\components;

use common\models\User;
use Yii;
use yii\filters\AccessControl;

final class AdminAccess extends AccessControl
{
    public function init(): void
    {
        $this->except = ['site/login', 'site/error', 'site/logout'];
        $this->rules = [[
            'allow' => true,
            'roles' => ['@'],
            'matchCallback' => static function (): bool {
                $identity = Yii::$app->user->identity;
                return $identity instanceof User && $identity->isAdmin();
            },
        ]];
        parent::init();
    }
}
