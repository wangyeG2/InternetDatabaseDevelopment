<?php

/** Backend administrator login form. Maintainer: B. */
declare(strict_types=1);

namespace backend\models;

use common\models\LoginForm as BaseLoginForm;

class LoginForm extends BaseLoginForm
{
    public function validatePassword(string $attribute, array|null $params): void
    {
        parent::validatePassword($attribute, $params);
        if (!$this->hasErrors() && !$this->getUser()?->isAdmin()) {
            $this->addError($attribute, 'This account does not have backend access.');
        }
    }
}
