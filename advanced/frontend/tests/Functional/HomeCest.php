<?php

/** Homepage navigation regression. Maintainer: C. */

declare(strict_types=1);

namespace frontend\tests\Functional;

use frontend\tests\Support\FunctionalTester;
use Yii;

final class HomeCest
{
    public function checkOpen(FunctionalTester $I): void
    {
        $I->amOnRoute(Yii::$app->homeUrl);
        $I->see('2026世界杯主题团队主页');
        $I->seeLink('关于本站');
        $I->click('关于本站');
        $I->see('This is the About page.');
    }
}
