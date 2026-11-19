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
        $I->seeNumberOfElements('.wc-result-card', 4);
        $I->see('西班牙', '.wc-lead-score');
        $I->see('1 : 0', '.wc-lead-score');
        $I->see('106', '.wc-lead-copy');
        $I->seeNumberOfElements('.wc-photo-story', 3);
        $I->click('阅读决赛战报');
        $I->see('加时完场', '.wc-match-page');
        $I->amOnRoute('site/index');
        $I->click('完整赛程');
        $I->see('全部赛程与赛果');
    }
}
