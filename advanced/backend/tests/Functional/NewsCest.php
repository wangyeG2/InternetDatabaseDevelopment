<?php

/** Admin/member route boundaries and news editor integration. Maintainer: B. */
declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\NewsScenarioFixture;
use common\models\News;

final class NewsCest
{
    public function _fixtures(): array
    {
        return ['newsScenario' => NewsScenarioFixture::class];
    }

    public function guestMustLogin(FunctionalTester $I): void
    {
        $I->amOnRoute('news/index');
        $I->see('Sign in to your account', 'h1');
        $I->dontSee('新闻公告管理', 'h1');
    }

    public function memberCannotLoginOrEnterManagement(FunctionalTester $I): void
    {
        $I->amOnRoute('site/login');
        $I->fillField('Your Username', 'news_member');
        $I->fillField('Your Password', 'password_0');
        $I->click('login-button');
        $I->see('This account does not have backend access.');
        $I->amLoggedInAs(2);
        foreach (['site/index', 'news/index', 'news/create', 'news-category/index'] as $route) {
            $I->amOnRoute($route);
            $I->seeResponseCodeIs(403);
        }
    }

    public function adminCanCreateAndEditNews(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('news/create');
        $I->submitForm('#news-form', ['NewsForm[title]' => '集成测试公告', 'NewsForm[content]' => '后台新建的正文。',
            'NewsForm[category_id]' => '1', 'NewsForm[status]' => '0', 'NewsForm[is_pinned]' => '1', 'NewsForm[tags]' => '课程,团队']);
        $I->see('集成测试公告', 'h1');
        $I->see('草稿');
        $news = News::find()->where(['title' => '集成测试公告'])->one();
        $I->amOnRoute('news/update', ['id' => $news->id]);
        $I->submitForm('#news-form', ['NewsForm[title]' => '已经发布的集成公告', 'NewsForm[status]' => '10']);
        $I->see('已经发布的集成公告', 'h1');
        $I->seeRecord(News::class, ['id' => $news->id, 'status' => 10, 'author_id' => 1]);
    }

    public function stateChangesRequirePost(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        foreach (['publish', 'withdraw', 'delete'] as $action) {
            $I->amOnRoute('news/' . $action, ['id' => 1]);
            $I->seeResponseCodeIs(405);
        }
        $I->seeRecord(News::class, ['id' => 1, 'status' => 10, 'deleted_at' => null]);
    }

    public function categoryCanBeCreatedAndDisabled(FunctionalTester $I): void
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('news-category/create');
        $I->submitForm('#news-category-form', ['NewsCategoryForm[name]' => '专题讨论', 'NewsCategoryForm[slug]' => 'discussion',
            'NewsCategoryForm[sort_order]' => '40', 'NewsCategoryForm[is_active]' => '0']);
        $I->see('专题讨论');
        $I->seeRecord(\common\models\NewsCategory::class, ['slug' => 'discussion', 'is_active' => 0]);
    }
}
