<?php

/** Explicit local provisioning for the first administrator. Maintainer: B. */

declare(strict_types=1);

namespace console\controllers;

use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class AccessController extends Controller
{
    /** Grant a role only to an existing, email-verified account. */
    public function actionGrantAdmin(string $username): int
    {
        $user = User::findByUsername($username);
        if ($user === null) {
            $this->stderr("An active, email-verified account with this username is required.\n");
            return ExitCode::DATAERR;
        }
        $user->role = User::ROLE_ADMIN;
        if (!$user->save()) {
            $this->stderr("The administrator role could not be saved.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->stdout("Administrator access granted to {$user->username}.\n");
        return ExitCode::OK;
    }
}
