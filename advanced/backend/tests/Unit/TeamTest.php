<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\tests\Unit;

use backend\models\TeamMemberForm;
use backend\models\TeamProfileForm;
use backend\models\TeamMilestoneForm;
use backend\models\TeamLinkForm;
use Codeception\Test\Unit;
use common\fixtures\TeamScenarioFixture;
use common\models\TeamMember;
use common\models\TeamProfile;
use common\models\TeamMilestone;
use common\models\TeamLink;
use common\models\User;
use Yii;

final class TeamTest extends Unit
{
    public function _fixtures(): array
    {
        return ['teamScenario' => TeamScenarioFixture::class];
    }

    public function testPublicQueriesAndOrdering(): void
    {
        $this->assertSame(['public-member'], TeamMember::find()->publiclyVisible()->ordered()->select('slug')->column());
        $this->assertSame(1, (int) TeamMilestone::find()->publiclyVisible()->count());
        $this->assertSame(1, (int) TeamLink::find()->publiclyVisible()->count());
        $this->assertSame('fixture-team', TeamProfile::current()->slug);
    }

    public function testMalformedAndIllegalBooleansAreRejected(): void
    {
        foreach ([['unexpected'], '2', '', 'true'] as $value) {
            $form = TeamMemberForm::forRecord(TeamMember::findOne(1));
            $form->load(['TeamMemberForm' => ['is_public' => $value]]);
            $this->assertFalse($form->validate());
            $this->assertArrayHasKey('is_public', $form->errors);
        }
    }

    public function testValidationAndUniqueSlug(): void
    {
        $form = new TeamMemberForm(['name' => '示例', 'slug' => 'public-member']);
        $this->assertFalse($form->validate());
        $form->slug = '../unsafe';
        $this->assertFalse($form->validate());
        $form->slug = 'valid-slug';
        $form->duties = str_repeat('中', 121) . ' | 示例';
        $this->assertFalse($form->validate());
        $form = new TeamLinkForm(['label' => '示例', 'url' => 'javascript:alert(1)']);
        $this->assertFalse($form->validate());
        $form->url = 'https://example.test/';
        $this->assertTrue($form->validate());
        $milestone = new TeamMilestoneForm(['title' => '示例', 'happened_on' => '2026-02-30']);
        $this->assertFalse($milestone->validate());
        $profile = TeamProfileForm::forRecord(TeamProfile::current());
        $profile->body = str_repeat('中', 12001);
        $this->assertFalse($profile->validate());
    }

    public function testNonAdminServiceCallIsRejected(): void
    {
        Yii::$app->user->login(User::findOne(2));
        try {
            (new \backend\services\TeamService())->save(TeamMemberForm::forRecord(TeamMember::findOne(1)));
            $this->fail('Expected access rejection.');
        } catch (\DomainException $exception) {
            $this->assertSame('仅激活管理员可编辑团队资料。', $exception->getMessage());
        } finally {
            Yii::$app->user->logout();
        }
    }

    public function testDutyWriteFailureRollsBackMemberAndOriginalDuties(): void
    {
        Yii::$app->user->login(User::findOne(1));
        $form = TeamMemberForm::forRecord(TeamMember::findOne(1));
        $form->name = '应回滚的示例姓名';
        $form->duties = '新职责 | 示例';
        $fail = static function (): void {
            throw new \RuntimeException('Simulated duty write failure');
        };
        \yii\base\Event::on(\common\models\TeamMemberDuty::class, \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, $fail);
        try {
            $form->save();
            $this->fail('Expected duty write failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated duty write failure', $exception->getMessage());
            $this->assertSame('示例成员1', TeamMember::findOne(1)->name);
            $this->assertSame('示例职责', TeamMember::findOne(1)->duties[0]->title);
        } finally {
            \yii\base\Event::off(\common\models\TeamMemberDuty::class, \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, $fail);
            Yii::$app->user->logout();
        }
    }

    public function testMemberDutiesSaveTogether(): void
    {
        Yii::$app->user->login(User::findOne(1));
        $form = TeamMemberForm::forRecord(TeamMember::findOne(1));
        $form->duties = "示例标题 | 示例说明\n另一职责 | 另一说明";
        $this->assertTrue($form->save());
        $this->assertCount(2, TeamMember::findOne(1)->duties);
        $form->duties = str_repeat('中', 121);
        $form->name = '不应保存';
        $this->assertFalse($form->save());
        $this->assertSame('示例成员1', TeamMember::findOne(1)->name);
        $this->assertCount(2, TeamMember::findOne(1)->duties);
        Yii::$app->user->logout();
    }
}
