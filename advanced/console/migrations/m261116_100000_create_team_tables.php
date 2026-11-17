<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

use yii\db\Migration;

class m261116_100000_create_team_tables extends Migration
{
    public function safeUp(): void
    {
        $options = $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB' : null;
        $this->createTable('{{%team_profile}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull()->defaultValue(''),
            'slug' => $this->string(80)->notNull()->defaultValue(''),
            'motto' => $this->string(180)->notNull()->defaultValue(''),
            'summary' => $this->string(500)->notNull()->defaultValue(''),
            'body' => $this->text()->notNull(),
            'theme_note' => $this->string(500)->notNull()->defaultValue(''),
            'is_current' => $this->boolean()->null()->defaultValue(null),
            'updated_by' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $options);
        $this->createIndex('ux_team_profile_slug', '{{%team_profile}}', 'slug', true);
        $this->createIndex('ux_team_profile_current', '{{%team_profile}}', 'is_current', true);
        $this->addCheck('ck_team_profile_current', '{{%team_profile}}', 'is_current IS NULL OR is_current = 1');
        $this->createIndex('ix_team_profile_updated_by', '{{%team_profile}}', 'updated_by');
        $this->addForeignKey('fk_team_profile_updated_by', '{{%team_profile}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->createTable('{{%team_member}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60)->notNull()->defaultValue(''),
            'slug' => $this->string(80)->notNull()->defaultValue(''),
            'module_code' => $this->string(1)->notNull()->defaultValue(''),
            'role_title' => $this->string(120)->notNull()->defaultValue(''),
            'duty' => $this->string(255)->notNull()->defaultValue(''),
            'bio' => $this->text()->notNull(),
            'user_id' => $this->integer()->null(),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_public' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $options);
        $this->createIndex('ux_team_member_slug', '{{%team_member}}', 'slug', true);
        $this->createIndex('ix_team_member_order', '{{%team_member}}', ['is_public', 'sort_order']);
        $this->createIndex('ix_team_member_user_id', '{{%team_member}}', 'user_id');
        $this->addForeignKey('fk_team_member_user_id', '{{%team_member}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->createTable('{{%team_member_duty}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(120)->notNull()->defaultValue(''),
            'detail' => $this->string(1000)->notNull()->defaultValue(''),
            'member_id' => $this->integer()->notNull(),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0)
        ], $options);
        $this->createIndex('ix_team_member_duty_order', '{{%team_member_duty}}', ['member_id', 'sort_order']);
        $this->createIndex('ix_team_member_duty_member_id', '{{%team_member_duty}}', 'member_id');
        $this->addForeignKey('fk_team_member_duty_member_id', '{{%team_member_duty}}', 'member_id', '{{%team_member}}', 'id', 'CASCADE', 'RESTRICT');
        $this->createTable('{{%team_milestone}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(180)->notNull()->defaultValue(''),
            'happened_on' => $this->string(10)->notNull()->defaultValue(''),
            'summary' => $this->string(1000)->notNull()->defaultValue(''),
            'is_example' => $this->boolean()->notNull()->defaultValue(0),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_public' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $options);
        $this->createIndex('ix_team_milestone_order', '{{%team_milestone}}', ['is_public', 'sort_order']);
        $this->createTable('{{%team_link}}', [
            'id' => $this->primaryKey(),
            'label' => $this->string(120)->notNull()->defaultValue(''),
            'url' => $this->string(2048)->notNull()->defaultValue(''),
            'sort_order' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_public' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ], $options);
        $this->createIndex('ix_team_link_order', '{{%team_link}}', ['is_public', 'sort_order']);
    }

    public function safeDown(): bool
    {
        echo "C migration is additive; automatic table deletion is disabled.\n";
        return false;
    }
}
