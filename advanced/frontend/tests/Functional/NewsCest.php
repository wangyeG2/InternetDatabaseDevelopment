<?php

/** Public browsing, visibility, search and stored-text escaping tests. Maintainer: B. */

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\NewsScenarioFixture;
use common\models\News;
use frontend\tests\Support\FunctionalTester;

final class NewsCest
{
    public function _fixtures(): array
    {
        return ['newsScenario' => NewsScenarioFixture::class];
    }

    public function listContainsOnlyPublicNews(FunctionalTester $I): void
    {
        $I->amOnRoute('news/index');
        $I->see('团队观赛活动通知');
        foreach (['未发布的内部草稿', '停用分类的文章', '归档文章', '已经删除的文章', '尚未到公开时间的文章'] as $title) {
            $I->dontSee($title);
        }
    }

    public function hiddenIdsCannotBeOpenedDirectly(FunctionalTester $I): void
    {
        foreach ([2, 3, 4, 5, 6, 999999] as $id) {
            $I->amOnRoute('news/view', ['id' => $id]);
            $I->seeResponseCodeIs(404);
        }
    }

    public function filtersAndInjectionTextAreHandledAsValues(FunctionalTester $I): void
    {
        $I->amOnRoute('news/index', ['q' => '观赛', 'category' => '1', 'tag' => '1']);
        $I->see('团队观赛活动通知');
        $I->amOnRoute('news/index', ['q' => "' OR 1=1 --"]);
        $I->see('暂无符合条件的新闻公告。');
        $I->amOnRoute('news/index', ['q' => ['invalid']]);
        $I->seeResponseCodeIs(200);
        $I->see('must be a single value.');
    }

    public function storedHtmlIsDisplayedAsText(FunctionalTester $I): void
    {
        News::updateAll(['content' => '<script id="stored-xss">alert(1)</script>'], ['id' => 1]);
        $I->amOnRoute('news/view', ['id' => 1]);
        $I->dontSeeElement('script#stored-xss');
        $I->seeInSource('&lt;script id=&quot;stored-xss&quot;&gt;');
    }

    public function malformedLoginIsAValidationError(FunctionalTester $I): void
    {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', ['LoginForm[username]' => ['unexpected-array'], 'LoginForm[password]' => 'test']);
        $I->seeResponseCodeIs(200);
        $I->see('must be a single value.');
    }

    public function paginationKeepsFiltersAndPinnedOrdering(FunctionalTester $I): void
    {
        $source = News::findOne(1)->attributes;
        unset($source['id']);
        for ($number = 1; $number <= 14; $number++) {
            $row = $source;
            $row['title'] = sprintf('分页新闻%02d', $number);
            $row['slug'] = 'pagination-' . $number;
            $row['is_pinned'] = $number === 1 ? 1 : 0;
            \Yii::$app->db->createCommand()->insert('{{%news}}', $row)->execute();
        }
        $I->amOnRoute('news/index', ['q' => '分页新闻', 'category' => '1']);
        $I->see('共 14 篇');
        $I->see('分页新闻01');
        $I->see('分页新闻14');
        $I->dontSee('分页新闻02');
        $I->click('2', '.pagination');
        $I->see('分页新闻02');
        $I->dontSee('分页新闻14');
        $I->seeInCurrentUrl('q=');
        $I->seeInCurrentUrl('category=1');
        $I->assertCount(2, $I->grabMultiple('.news-item'));
    }
}
