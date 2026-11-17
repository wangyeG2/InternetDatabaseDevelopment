<?php

/** Homepage navigation regression. Maintainer: C. */

declare(strict_types=1);

namespace frontend\tests\Acceptance;

use frontend\tests\Support\AcceptanceTester;

final class HomeCest
{
    public function checkHome(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->see('2026世界杯主题团队主页');

        $I->seeLink('关于本站');
        $I->click('关于本站');

        $I->see('This is the About page.');
    }
}
