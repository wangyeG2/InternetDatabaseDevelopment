<?php

/** Create the moderated message board table. Maintainer: D (秦东杰). */

declare(strict_types=1);

use yii\db\Migration;

class m261118_100000_create_message_table extends Migration
{
    public function safeUp(): void
    {
        $options = $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB' : null;
        $this->createTable('{{%message}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'topic' => $this->string(24)->notNull(),
            'body' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'moderated_by' => $this->integer()->null(),
            'moderated_at' => $this->integer()->null(),
        ], $options);
        $this->createIndex('ix_message_status_id', '{{%message}}', ['status', 'id']);
        $this->createIndex('ix_message_status_topic', '{{%message}}', ['status', 'topic']);
        $this->addForeignKey('fk_message_author', '{{%message}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk_message_moderator', '{{%message}}', 'moderated_by', '{{%user}}', 'id', 'SET NULL', 'RESTRICT');
    }

    public function safeDown(): bool
    {
        echo "Automatic deletion of saved messages is disabled.\n";
        return false;
    }
}
