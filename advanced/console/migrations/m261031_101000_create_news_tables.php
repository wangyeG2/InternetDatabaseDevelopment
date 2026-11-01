<?php

/** News categories, posts, tags and edit history. Maintainer: B. */

declare(strict_types=1);

use yii\db\Migration;

class m261031_101000_create_news_tables extends Migration
{
    public function safeUp(): void
    {
        $options = $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB' : null;
        $this->createTable('{{%news_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60)->notNull()->unique(),
            'slug' => $this->string(80)->notNull()->unique(),
            'description' => $this->string(255)->notNull()->defaultValue(''),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $options);
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'title' => $this->string(180)->notNull(),
            'slug' => $this->string(96)->notNull()->unique(),
            'summary' => $this->string(360)->notNull()->defaultValue(''),
            'content' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_pinned' => $this->boolean()->notNull()->defaultValue(false),
            'published_at' => $this->integer(),
            'deleted_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $options);
        $this->createIndex('idx_news_public_list', '{{%news}}', ['status', 'deleted_at', 'is_pinned', 'published_at']);
        $this->createIndex('idx_news_category', '{{%news}}', 'category_id');
        $this->createIndex('idx_news_author', '{{%news}}', 'author_id');
        $this->addForeignKey('fk_news_category', '{{%news}}', 'category_id', '{{%news_category}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_news_author', '{{%news}}', 'author_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->createTable('{{%news_tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
        ], $options);
        $this->createTable('{{%news_tag_link}}', [
            'news_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
            'PRIMARY KEY ([[news_id]], [[tag_id]])',
        ], $options);
        $this->createIndex('idx_news_tag_link_tag', '{{%news_tag_link}}', 'tag_id');
        $this->addForeignKey('fk_news_tag_link_news', '{{%news_tag_link}}', 'news_id', '{{%news}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_news_tag_link_tag', '{{%news_tag_link}}', 'tag_id', '{{%news_tag}}', 'id', 'CASCADE', 'CASCADE');
        $this->createTable('{{%news_revision}}', [
            'id' => $this->primaryKey(),
            'news_id' => $this->integer()->notNull(),
            'editor_id' => $this->integer()->notNull(),
            'operation' => $this->string(16)->notNull(),
            'snapshot' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $options);
        $this->createIndex('idx_news_revision_post', '{{%news_revision}}', ['news_id', 'created_at']);
        $this->createIndex('idx_news_revision_editor', '{{%news_revision}}', 'editor_id');
        $this->addForeignKey('fk_news_revision_news', '{{%news_revision}}', 'news_id', '{{%news}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_news_revision_editor', '{{%news_revision}}', 'editor_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

        $now = time();
        $this->batchInsert('{{%news_category}}', ['name', 'slug', 'description', 'sort_order', 'is_active', 'created_at', 'updated_at'], [
            ['赛事资讯', 'tournament', '赛事主题与学习资料。', 10, 1, $now, $now],
            ['团队公告', 'team', '团队安排与项目进展。', 20, 1, $now, $now],
            ['观赛指南', 'guides', '观赛活动与规则介绍。', 30, 1, $now, $now],
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%news_revision}}');
        $this->dropTable('{{%news_tag_link}}');
        $this->dropTable('{{%news_tag}}');
        $this->dropTable('{{%news}}');
        $this->dropTable('{{%news_category}}');
    }
}
