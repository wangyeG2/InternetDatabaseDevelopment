<?php

/** Isolated integration data for the B news flow; never load in development. Maintainer: B. */
declare(strict_types=1);

namespace common\fixtures;

use RuntimeException;
use Yii;
use yii\test\Fixture;

class NewsScenarioFixture extends Fixture
{
    public function load(): void
    {
        if (!YII_ENV_TEST || !str_contains(Yii::$app->db->dsn, 'test')) {
            throw new RuntimeException('NewsScenarioFixture requires a dedicated test database.');
        }
        $this->clear();
        $now = time();
        $hash = '$2y$13$nJ1WDlBaGcbCdbNC5.5l4.sgy.OMEKCqtDQOdQ2OWpgiKRWYyzzne'; // Test-only password_0.
        foreach ([[1, 'news_admin', 'admin'], [2, 'news_member', 'member'], [3, 'other_admin', 'admin']] as [$id, $name, $role]) {
            Yii::$app->db->createCommand()->insert('{{%user}}', [
                'id' => $id, 'username' => $name, 'email' => $name . '@example.test',
                'password_hash' => $hash, 'auth_key' => 'fixture-key-' . $id, 'status' => 10,
                'role' => $role, 'created_at' => $now, 'updated_at' => $now,
            ])->execute();
        }
        foreach ([[1, '团队公告', 'team', 1], [2, '停用分类', 'inactive', 0]] as [$id, $name, $slug, $active]) {
            Yii::$app->db->createCommand()->insert('{{%news_category}}', [
                'id' => $id, 'name' => $name, 'slug' => $slug, 'is_active' => $active,
                'sort_order' => $id, 'description' => '', 'created_at' => $now, 'updated_at' => $now,
            ])->execute();
        }
        $rows = [
            [1, '团队观赛活动通知', 'published-notice', 1, 10, $now - 100, null],
            [2, '未发布的内部草稿', 'internal-draft', 1, 0, null, null],
            [3, '停用分类的文章', 'inactive-category', 2, 10, $now - 100, null],
            [4, '归档文章', 'archived-post', 1, 20, null, null],
            [5, '已经删除的文章', 'deleted-post', 1, 10, $now - 100, $now],
            [6, '尚未到公开时间的文章', 'future-post', 1, 10, $now + 86400, null],
        ];
        foreach ($rows as [$id, $title, $slug, $category, $status, $published, $deleted]) {
            Yii::$app->db->createCommand()->insert('{{%news}}', [
                'id' => $id, 'title' => $title, 'slug' => $slug, 'category_id' => $category, 'author_id' => 1,
                'summary' => '活动安排与报名说明', 'content' => "第一段新闻正文。\n第二段说明。", 'status' => $status,
                'is_pinned' => 0, 'published_at' => $published, 'deleted_at' => $deleted,
                'created_at' => $now - 200, 'updated_at' => $now - 100,
            ])->execute();
        }
        Yii::$app->db->createCommand()->insert('{{%news_tag}}', ['id' => 1, 'name' => '团队', 'created_at' => $now])->execute();
        Yii::$app->db->createCommand()->insert('{{%news_tag_link}}', ['news_id' => 1, 'tag_id' => 1])->execute();
    }

    public function unload(): void
    {
        $this->clear();
    }

    private function clear(): void
    {
        foreach (['news_revision', 'news_tag_link', 'news', 'news_tag', 'news_category', 'user'] as $table) {
            Yii::$app->db->createCommand()->delete('{{%' . $table . '}}')->execute();
        }
    }
}
