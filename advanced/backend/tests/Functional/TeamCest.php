<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\TeamScenarioFixture;
use common\models\TeamMember;
use common\models\TeamProfile;
use yii\helpers\Url;

final class TeamCest
{
    public function _fixtures(): array
    {
        return ['teamScenario' => TeamScenarioFixture::class];
    }

    public function guestAndMemberBoundaries(FunctionalTester $I): void
    {
        $I->amOnRoute('team-member/update', ['id' => 1]);
        $I->see('Sign in to your account', 'h1');
        $I->amLoggedInAs(2);
        foreach (['team-profile/update', 'team-member/index', 'team-member/create', 'team-member/update',
            'team-milestone/create', 'team-link/create'] as $route) {
            $I->amOnRoute($route, ['id' => 1]);
            $I->seeResponseCodeIs(403);
        }
    }

    public function adminCanEditAndInvalidInputShowsErrors(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('team-profile/update');
        $I->submitForm('#team-form', ['TeamProfileForm[name]' => '示例更新团队']);
        $I->see('团队数据已保存。');
        $I->seeRecord(TeamProfile::class, ['id' => 1, 'name' => '示例更新团队', 'updated_by' => 1]);
        $I->amOnRoute('team-member/update', ['id' => 1]);
        $I->submitForm('#team-form', ['TeamMemberForm[name]' => '示例更新成员',
            'TeamMemberForm[duties]' => '示例职责 | 更新说明', 'TeamMemberForm[is_public]' => '1']);
        $I->see('团队数据已保存。');
        $I->seeRecord(TeamMember::class, ['id' => 1, 'name' => '示例更新成员']);
        $I->amOnRoute('team-member/update', ['id' => 1]);
        $I->submitForm('#team-form', ['TeamMemberForm[slug]' => '../invalid', 'TeamMemberForm[is_public]' => '2']);
        $I->seeElement('.error-summary');
        $I->seeRecord(TeamMember::class, ['id' => 1, 'slug' => 'public-member']);
    }

    public function adminCanCreateRosterMilestonesAndLinks(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('team-member/create');
        $I->submitForm('#team-form', ['TeamMemberForm[name]' => '示例新成员',
            'TeamMemberForm[slug]' => 'created-member', 'TeamMemberForm[is_public]' => '1']);
        $I->seeRecord(TeamMember::class, ['slug' => 'created-member', 'is_public' => 1]);
        $I->amOnRoute('team-milestone/create');
        $I->submitForm('#team-form', ['TeamMilestoneForm[title]' => '示例新计划',
            'TeamMilestoneForm[happened_on]' => '2026-11-28', 'TeamMilestoneForm[is_example]' => '1']);
        $I->seeRecord(\common\models\TeamMilestone::class, ['title' => '示例新计划', 'is_example' => 1]);
        $I->amOnRoute('team-link/create');
        $I->submitForm('#team-form', ['TeamLinkForm[label]' => '示例新链接', 'TeamLinkForm[url]' => 'javascript:alert(1)']);
        $I->seeElement('.error-summary');
        $I->submitForm('#team-form', ['TeamLinkForm[url]' => 'https://example.test/new']);
        $I->seeRecord(\common\models\TeamLink::class, ['label' => '示例新链接', 'url' => 'https://example.test/new']);
    }

    public function changesRequirePostAndCsrf(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        foreach (['team-member', 'team-milestone', 'team-link'] as $controller) {
            $I->amOnRoute($controller . '/hide', ['id' => 1]);
            $I->seeResponseCodeIs(405);
            $I->amOnRoute($controller . '/index');
            $I->sendAjaxPostRequest(Url::to(['/' . $controller . '/hide', 'id' => 1]));
            $I->seeResponseCodeIs(400);
        }
        $I->amOnRoute('team-member/index');
        $I->sendAjaxPostRequest(Url::to(['/team-member/hide', 'id' => 1]), [
            \Yii::$app->request->csrfParam => $I->grabAttributeFrom('meta[name="csrf-token"]', 'content'),
        ]);
        $I->seeRecord(TeamMember::class, ['id' => 1, 'is_public' => 0]);
        $I->amOnRoute('team-profile/update');
        $I->sendAjaxPostRequest(Url::to(['/team-profile/update']), ['TeamProfileForm' => ['name' => '不能保存']]);
        $I->seeResponseCodeIs(400);
    }
}
