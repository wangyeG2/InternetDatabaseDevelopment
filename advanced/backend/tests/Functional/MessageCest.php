<?php

/** Administrator access and moderation workflow checks. Maintainer: D (秦东杰). */

declare(strict_types=1);

namespace backend\tests\Functional;

use backend\tests\Support\FunctionalTester;
use common\fixtures\NewsScenarioFixture;
use common\models\Message;
use yii\helpers\Url;

final class MessageCest
{
    public function _fixtures(): array
    {
        return ['users' => NewsScenarioFixture::class];
    }

    public function queueIsRestrictedToAdministrators(FunctionalTester $I): void
    {
        $I->amOnRoute('message/index');
        $I->see('Sign in to your account', 'h1');
        $I->amLoggedInAs(2);
        $I->amOnRoute('message/index');
        $I->seeResponseCodeIs(403);
    }

    public function moderatorCanPublishAndHideUsingPost(FunctionalTester $I): void
    {
        $message = new Message(['topic' => 'team', 'body' => '一起讨论课程项目。']);
        $I->assertTrue($message->submit(2));
        $I->amLoggedInAs(1);
        $I->amOnRoute('message/publish', ['id' => $message->id]);
        $I->seeResponseCodeIs(405);
        $I->amOnRoute('message/index');
        $I->sendAjaxPostRequest(Url::to(['/message/publish', 'id' => $message->id]), ['originalStatus' => '0']);
        $I->seeResponseCodeIs(400);

        $I->amOnRoute('message/index');
        $I->see('一起讨论课程项目。');
        $I->submitForm('.message-publish-form', []);
        $I->seeRecord(Message::class, ['id' => $message->id, 'status' => Message::PUBLISHED, 'moderated_by' => 1]);
        $I->amOnRoute('message/index', ['status' => 'published']);
        $I->submitForm('.message-hide-form', ['originalStatus' => '0']);
        $I->see('留言状态已变更');
        $I->seeRecord(Message::class, ['id' => $message->id, 'status' => Message::PUBLISHED]);
        $I->submitForm('.message-hide-form', []);
        $I->seeRecord(Message::class, ['id' => $message->id, 'status' => Message::HIDDEN]);
        $I->amOnRoute('message/index', ['status' => 'hidden']);
        $I->see('一起讨论课程项目。');
    }
}
