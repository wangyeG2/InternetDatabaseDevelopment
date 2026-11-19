<?php

/** Tournament archive and knockout result regressions. Maintainer: 秦东杰. */

declare(strict_types=1);

namespace frontend\tests\Functional;

use frontend\tests\Support\FunctionalTester;

final class CupArchiveCest
{
    public function filtersPreserveEveryMatch(FunctionalTester $I): void
    {
        $I->amOnRoute('data/matches');
        $I->seeNumberOfElements('.wc-fixture', 104);
        $I->amOnRoute('data/matches', ['pool' => 'A']);
        $I->seeNumberOfElements('.wc-fixture', 6);
        $I->amOnRoute('data/matches', ['stage' => '决赛']);
        $I->seeNumberOfElements('.wc-fixture', 1);
        $I->amOnRoute('data/matches', ['pool' => 'Z']);
        $I->seeResponseCodeIs(404);
    }

    public function knockoutScoresStaySeparate(FunctionalTester $I): void
    {
        $I->amOnRoute('data/match', ['id' => 104]);
        $I->see('常规时间　0 : 0');
        $I->see('加时完场　1 : 0');
        $I->see('Ferran Torres');
        $I->amOnRoute('data/match', ['id' => 74]);
        $I->see('点球大战　3 : 4');
        $I->amOnRoute('data/match', ['id' => 999]);
        $I->seeResponseCodeIs(404);
    }
}
