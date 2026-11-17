<?php

/** Login and shared navigation regression. Maintainers: C/A. */

declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\UserFixture;

final class LoginCest
{
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php',
            ],
        ];
    }

    public function loginUser(FunctionalTester $I): void
    {
        $I->amOnRoute('/site/login');
        $I->fillField('Your Username', 'erau');
        $I->fillField('Your Password', 'password_0');
        $I->click('login-button');

        $I->seeLink('退出 (erau)');
        $I->dontSeeLink('登录');
        $I->dontSeeLink('注册');
    }
}
