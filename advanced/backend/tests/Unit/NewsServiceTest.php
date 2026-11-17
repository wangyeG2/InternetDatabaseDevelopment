<?php

/** News lifecycle, access control and author ownership tests. Maintainer: B. */

declare(strict_types=1);

namespace backend\tests\Unit;

use backend\models\NewsForm;
use backend\services\NewsService;
use Codeception\Test\Unit;
use common\fixtures\NewsScenarioFixture;
use common\models\News;
use common\models\NewsRevision;
use common\models\User;
use DomainException;
use RuntimeException;
use yii\base\Event;
use yii\db\ActiveRecord;

final class NewsServiceTest extends Unit
{
    public function _fixtures(): array
    {
        return ['newsScenario' => NewsScenarioFixture::class];
    }

    private function validForm(): NewsForm
    {
        return new NewsForm(['title' => '新的团队公告', 'content' => "公告正文。\n第二段。", 'summary' => '公告摘要',
            'category_id' => '1', 'tags' => '足球,团队,足球']);
    }

    public function testCompletePublicationLifecycleRetainsHistory(): void
    {
        $service = new NewsService();
        $editor = User::findOne(1);
        $news = $service->save($this->validForm(), $editor);
        $this->assertInstanceOf(News::class, $news);
        $this->assertSame(0, (int) $news->status);
        $this->assertCount(2, $news->tags);
        $this->assertSame(1, (int) NewsRevision::find()->where(['news_id' => $news->id])->count());
        $this->assertNull(News::find()->publiclyVisible()->andWhere(['n.id' => $news->id])->one());
        $service->publish($news, $editor);
        $this->assertNotNull(News::find()->publiclyVisible()->andWhere(['n.id' => $news->id])->one());
        $service->publish($news, $editor);
        $this->assertSame(2, (int) NewsRevision::find()->where(['news_id' => $news->id])->count());
        $service->withdraw($news, $editor);
        $this->assertNull(News::find()->publiclyVisible()->andWhere(['n.id' => $news->id])->one());
        $service->delete($news, $editor);
        $this->assertNotNull(News::findOne($news->id)->deleted_at);
        $this->assertSame(4, (int) NewsRevision::find()->where(['news_id' => $news->id])->count());
    }

    public function testEditingPreservesOriginalAuthorAndUpdatesTags(): void
    {
        $news = News::findOne(1);
        $form = NewsForm::forRecord($news);
        $form->load(['NewsForm' => ['title' => '更新后的公告', 'tags' => '项目,项目,文档', 'author_id' => '2']]);
        $saved = (new NewsService())->save($form, User::findOne(3));
        $this->assertNotNull($saved);
        $this->assertSame(1, (int) $saved->author_id);
        $this->assertSame(['文档', '项目'], $saved->getTagNames());
        $revision = NewsRevision::find()->where(['news_id' => 1])->one();
        $this->assertSame(3, (int) $revision->editor_id);
    }

    public function testInvalidAndExcessiveTagsDoNotCreatePosts(): void
    {
        $count = News::find()->count();
        $form = $this->validForm();
        $form->tags = implode(',', range(1, 11));
        $this->assertNull((new NewsService())->save($form, User::findOne(1)));
        $this->assertArrayHasKey('tags', $form->getErrors());
        $this->assertSame($count, News::find()->count());
        $form = $this->validForm();
        $form->category_id = '99999';
        $this->assertNull((new NewsService())->save($form, User::findOne(1)));
    }

    public function testMemberCannotUseManagementServiceDirectly(): void
    {
        $this->expectException(DomainException::class);
        (new NewsService())->save($this->validForm(), User::findOne(2));
    }

    public function testAuditFailureRollsBackNewsAndTags(): void
    {
        $news = News::findOne(1);
        $originalTitle = $news->title;
        $originalTags = $news->getTagNames();
        $form = NewsForm::forRecord($news);
        $form->title = '应当整体回滚的修改';
        $form->tags = 'rollback-only-tag';
        $failAudit = static function (): void {
            throw new RuntimeException('Simulated audit write failure');
        };
        Event::on(NewsRevision::class, ActiveRecord::EVENT_BEFORE_INSERT, $failAudit);
        try {
            (new NewsService())->save($form, User::findOne(1));
            $this->fail('Expected audit failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated audit write failure', $exception->getMessage());
        } finally {
            Event::off(NewsRevision::class, ActiveRecord::EVENT_BEFORE_INSERT, $failAudit);
        }
        $restored = News::findOne(1);
        $this->assertSame($originalTitle, $restored->title);
        $this->assertSame($originalTags, $restored->getTagNames());
        $this->assertFalse(\common\models\NewsTag::find()->where(['name' => 'rollback-only-tag'])->exists());
        $this->assertSame(0, (int) NewsRevision::find()->where(['news_id' => 1])->count());
    }

    public function testMaximumMultibyteContentAndAuditSnapshotAreStored(): void
    {
        $form = $this->validForm();
        $form->content = str_repeat('中⚽', 25000);
        $news = (new NewsService())->save($form, User::findOne(1));
        $this->assertNotNull($news);
        $this->assertSame($form->content, News::findOne($news->id)->content);
        $revision = NewsRevision::find()->where(['news_id' => $news->id])->one();
        $this->assertSame($form->content, \yii\helpers\Json::decode($revision->snapshot)['content']);
    }
}
