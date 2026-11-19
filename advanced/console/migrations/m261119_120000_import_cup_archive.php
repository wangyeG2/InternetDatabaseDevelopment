<?php

/** Import the team's complete tournament archive. Maintainer: 秦东杰. */

declare(strict_types=1);

use yii\db\Migration;

class m261119_120000_import_cup_archive extends Migration
{
    public function safeUp(): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/cup2026.json'), true, 512, JSON_THROW_ON_ERROR);
        $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        $this->createTable('{{%cup_team}}', [
            'code' => $this->string(6)->notNull(), 'name' => $this->string(40)->notNull(), 'PRIMARY KEY (code)',
        ], $options);
        $this->createTable('{{%cup_match}}', [
            'id' => $this->integer()->notNull(), 'PRIMARY KEY (id)',
            'stage' => $this->string(32)->notNull(), 'pool' => $this->string(1)->notNull(),
            'match_date' => $this->date()->notNull(), 'local_time' => $this->string(20)->notNull(),
            'kickoff' => $this->integer()->notNull(), 'city' => $this->string(60)->notNull(),
            'home_code' => $this->string(6)->notNull(), 'away_code' => $this->string(6)->notNull(),
            'scores' => $this->text()->notNull(), 'goals' => $this->text()->notNull(),
        ], $options);
        $this->createIndex('ix_cup_kickoff', '{{%cup_match}}', 'kickoff');
        foreach (['home_code', 'away_code'] as $column) {
            $this->addForeignKey('fk_cup_' . $column, '{{%cup_match}}', $column, '{{%cup_team}}', 'code');
        }
        foreach ($data as $table => $seed) {
            $this->batchInsert('{{%' . $table . '}}', $seed['columns'], $seed['rows']);
        }
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%cup_match}}');
        $this->dropTable('{{%cup_team}}');
    }
}
