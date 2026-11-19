<?php

/** Homepage navigation regression. Maintainer: C. */

declare(strict_types=1);

namespace frontend\tests\Functional;

use frontend\tests\Support\FunctionalTester;

final class HomeCest
{
    public function checkOpen(FunctionalTester $I): void
    {
        $I->amOnRoute('site/index');
        $I->seeResponseCodeIs(200);
        $I->see('2026世界杯主题团队主页');
        $I->dontSeeLink('关于本站');
        $I->dontSeeLink('联系');
        $I->click('完整赛程');
        $I->see('全部赛程与赛果');
    }
}
