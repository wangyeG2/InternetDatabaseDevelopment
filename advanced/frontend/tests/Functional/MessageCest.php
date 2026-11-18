<?php

/** Message submission, visibility and chart checks. Maintainer: D (秦东杰). */

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\fixtures\NewsScenarioFixture;
use common\models\Message;
use frontend\tests\Support\FunctionalTester;

final class MessageCest
{
    public function _fixtures(): array
    {
        return ['users' => NewsScenarioFixture::class];
    }

    public function memberSubmissionRequiresModeration(FunctionalTester $I): void
    {
        $I->amOnRoute('message/index');
        $I->see('登录后即可提交留言');
        $I->dontSeeElement('#message-form');

        $I->amLoggedInAs(2);
        $I->amOnRoute('message/index');
        $I->submitForm('#message-form', [
            'Message[topic]' => 'matches',
            'Message[body]' => '<b id="board-text">我想聊世界杯</b>',
            'Message[status]' => '10',
            'Message[author_id]' => '1',
        ]);
        $I->see('留言已提交');
        $I->seeRecord(Message::class, ['author_id' => 2, 'status' => Message::PENDING]);
        $I->dontSee('我想聊世界杯');
        $I->seeElement('[data-chart-count="0"]');

        Message::updateAll(['status' => Message::PUBLISHED], ['author_id' => 2]);
        $I->amOnRoute('message/index');
        $I->see('我想聊世界杯');
        $I->dontSeeElement('#board-text');
        $I->seeElement('[data-chart-count="1"]');
        $I->see('已公开 1 条留言');

        Message::updateAll(['status' => Message::HIDDEN], ['author_id' => 2]);
        $I->amOnRoute('message/index');
        $I->dontSee('我想聊世界杯');
        $I->see('已公开 0 条留言');
    }

    public function malformedFieldsRerenderWithValidationErrors(FunctionalTester $I): void
    {
        $I->amLoggedInAs(2);
        $I->amOnRoute('message/index');
        $I->submitForm('#message-form', ['Message[topic]' => ['bogus'], 'Message[body]' => [['bogus']]]);
        $I->seeResponseCodeIs(200);
        $I->see('请为话题提供单个文本值。');
        $I->see('请为留言提供单个文本值。');
        $I->dontSeeRecord(Message::class, ['author_id' => 2]);
    }
}
