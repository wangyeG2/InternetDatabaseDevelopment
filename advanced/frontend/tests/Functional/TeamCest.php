<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\TeamScenarioFixture;
use common\models\TeamMember;
use frontend\tests\Support\FunctionalTester;

final class TeamCest
{
    public function _fixtures(): array
    {
        return ['teamScenario' => TeamScenarioFixture::class];
    }

    public function publicPagesAndNavigation(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index');
        foreach (['首页', '团队介绍', '成员', '新闻公告', '作业下载'] as $label) {
            $I->seeLink($label);
        }
        $I->amOnRoute('team/index');
        $I->see('示例测试团队');
        $I->see('示例成员1');
        $I->dontSee('示例成员2');
        $I->see('示例节点1');
        $I->dontSee('示例节点0');
        $I->dontSee('示例链接0');
        $I->amOnRoute('team/member', ['slug' => 'public-member']);
        $I->see('示例个人介绍');
        $I->see('示例职责说明');
        foreach (['hidden-member', 'missing'] as $slug) {
            $I->amOnRoute('team/member', ['slug' => $slug]);
            $I->seeResponseCodeIs(404);
        }
        foreach (['news/index', 'download/index'] as $route) {
            $I->amOnRoute($route);
            $I->seeResponseCodeIs(200);
        }
    }

    public function textIsEscapedAndEmptyStateIsHonest(FunctionalTester $I): void
    {
        TeamMember::updateAll(['bio' => '<script id="team-xss">alert(1)</script>'], ['id' => 1]);
        $I->amOnRoute('team/member', ['slug' => 'public-member']);
        $I->dontSeeElement('script#team-xss');
        $I->seeInSource('&lt;script id=&quot;team-xss&quot;&gt;');
        TeamMember::updateAll(['is_public' => 0]);
        $I->amOnRoute('team/index');
        $I->see('暂无公开成员资料。');
    }
}
