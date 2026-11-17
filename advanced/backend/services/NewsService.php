<?php

/** Transactional news editing, tag synchronization and history. Maintainer: B. */

declare(strict_types=1);

namespace backend\services;

use backend\models\NewsForm;
use common\models\News;
use common\models\NewsRevision;
use common\models\NewsTag;
use common\models\NewsTagLink;
use common\models\User;
use DomainException;
use RuntimeException;
use Throwable;
use yii\db\IntegrityException;
use yii\helpers\Json;

class NewsService
{
    public function save(NewsForm $form, User $editor): ?News
    {
        $this->requireAdmin($editor);
        if (!$form->validate()) {
            return null;
        }
        $news = $form->record ?? new News();
        if ($news->deleted_at !== null) {
            throw new DomainException('Deleted news cannot be edited.');
        }
        $created = $news->isNewRecord;
        $transaction = News::getDb()->beginTransaction();
        try {
            foreach (['title', 'summary', 'content'] as $field) {
                $news->$field = $form->$field;
            }
            $news->slug = $form->slug !== '' ? $form->slug : 'news-' . bin2hex(random_bytes(8));
            $news->category_id = (int) $form->category_id;
            $news->status = (int) $form->status;
            $news->is_pinned = (int) $form->is_pinned;
            if ($created) {
                $news->author_id = $editor->id;
            }
            $news->published_at = $news->status === News::STATUS_PUBLISHED ? ($news->published_at ?? time()) : null;
            if (!$news->save()) {
                $form->addErrors($news->getErrors());
                $transaction->rollBack();
                return null;
            }
            $this->synchronizeTags($news, $form->tagNames());
            $this->recordRevision($news, $editor, $created ? 'create' : 'update');
            $transaction->commit();
            return $news;
        } catch (IntegrityException $exception) {
            $transaction->rollBack();
            $form->addError('title', '保存时数据发生冲突，请检查标识和分类后重试。');
            return null;
        } catch (Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }
    }

    public function publish(News $news, User $editor): void
    {
        $this->changeState($news, $editor, News::STATUS_PUBLISHED, 'publish');
    }

    public function withdraw(News $news, User $editor): void
    {
        $this->changeState($news, $editor, News::STATUS_DRAFT, 'withdraw');
    }

    public function delete(News $news, User $editor): void
    {
        $this->requireAdmin($editor);
        News::getDb()->transaction(function () use ($news, $editor): void {
            $news->deleted_at = time();
            $news->status = News::STATUS_ARCHIVED;
            $news->published_at = null;
            if (!$news->save()) {
                throw new RuntimeException('News deletion could not be saved.');
            }
            $this->recordRevision($news, $editor, 'delete');
        });
    }

    private function changeState(News $news, User $editor, int $status, string $operation): void
    {
        $this->requireAdmin($editor);
        if ($news->deleted_at !== null) {
            throw new DomainException('Deleted news cannot change state.');
        }
        if ((int) $news->status === $status) {
            return;
        }
        News::getDb()->transaction(function () use ($news, $editor, $status, $operation): void {
            $news->status = $status;
            $news->published_at = $status === News::STATUS_PUBLISHED ? time() : null;
            if (!$news->save()) {
                throw new RuntimeException('News publication state could not be saved.');
            }
            $this->recordRevision($news, $editor, $operation);
        });
    }

    private function synchronizeTags(News $news, array $names): void
    {
        NewsTagLink::deleteAll(['news_id' => $news->id]);
        foreach ($names as $name) {
            $tag = NewsTag::findOne(['name' => $name]);
            if ($tag === null) {
                $tag = new NewsTag(['name' => $name, 'created_at' => time()]);
                if (!$tag->save()) {
                    throw new RuntimeException('News tag could not be saved.');
                }
            }
            $link = new NewsTagLink(['news_id' => $news->id, 'tag_id' => $tag->id]);
            if (!$link->save(false)) {
                throw new RuntimeException('News tag relation could not be saved.');
            }
        }
        $news->populateRelation('tags', NewsTag::find()->innerJoin('{{%news_tag_link}} l', 'l.tag_id = {{%news_tag}}.id')
            ->where(['l.news_id' => $news->id])->orderBy(['name' => SORT_ASC])->all());
    }

    private function recordRevision(News $news, User $editor, string $operation): void
    {
        $snapshot = $news->getAttributes();
        $snapshot['tags'] = $news->getTagNames();
        $revision = new NewsRevision([
            'news_id' => $news->id, 'editor_id' => $editor->id, 'operation' => $operation,
            'snapshot' => Json::encode($snapshot), 'created_at' => time(),
        ]);
        if (!$revision->save(false)) {
            throw new RuntimeException('News history could not be saved.');
        }
    }

    private function requireAdmin(User $editor): void
    {
        if (!$editor->isAdmin()) {
            throw new DomainException('Administrator access is required to change news.');
        }
    }
}
