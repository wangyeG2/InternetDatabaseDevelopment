<?php

/** Administrator account management regression tests. Maintainer: A. */

declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\UserFixture;
use common\models\User;

final class UserCest
{
    public function _fixtures(): array
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user_management_data.php',
            ],
        ];
    }

    public function guestsAndMembersCannotManageAccounts(FunctionalTester $I): void
    {
        $I->amOnRoute('user/index');
        $I->see('Sign in to your account', 'h1');

        $I->amLoggedInAs(2);
        $I->amOnRoute('user/index');
        $I->seeResponseCodeIs(403);
    }

    public function administratorCanChangeRoleAndStatus(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('user/index');
        $I->see('site-member');

        $I->amOnRoute('user/update', ['id' => 2]);
        $I->submitForm('#user-form', [
            'UserManagementForm[role]' => User::ROLE_ADMIN,
            'UserManagementForm[status]' => (string) User::STATUS_ACTIVE,
        ]);
        $I->see('用户权限与状态已保存。');
        $I->seeRecord(User::class, [
            'id' => 2,
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        $I->amOnRoute('user/update', ['id' => 2]);
        $I->submitForm('#user-form', [
            'UserManagementForm[role]' => 'invalid',
            'UserManagementForm[status]' => '1',
        ]);
        $I->seeElement('.error-summary');
        $I->seeRecord(User::class, [
            'id' => 2,
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function administratorCannotRemoveTheirOwnAccess(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('user/update', ['id' => 1]);
        $I->submitForm('#user-form', [
            'UserManagementForm[role]' => User::ROLE_MEMBER,
            'UserManagementForm[status]' => (string) User::STATUS_INACTIVE,
        ]);

        $I->seeElement('.error-summary');
        $I->seeRecord(User::class, [
            'id' => 1,
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
