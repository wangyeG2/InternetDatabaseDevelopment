<?php

/** Match the 50,000-character editor limit for UTF-8 content and audit JSON. Maintainer: B. */

declare(strict_types=1);

use yii\db\Migration;

class m261101_100000_expand_news_text_capacity extends Migration
{
    public function safeUp(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->alterColumn('{{%news}}', 'content', 'MEDIUMTEXT NOT NULL');
            $this->alterColumn('{{%news_revision}}', 'snapshot', 'MEDIUMTEXT NOT NULL');
        }
    }

    public function safeDown(): bool
    {
        if ($this->db->driverName === 'mysql') {
            foreach (['news' => 'content', 'news_revision' => 'snapshot'] as $table => $column) {
                if (
                    (new \yii\db\Query())->from('{{%' . $table . '}}')
                    ->where('OCTET_LENGTH([[' . $column . ']]) > 65535')->exists($this->db)
                ) {
                    echo "Cannot reduce text capacity while long news or history exists.\n";
                    return false;
                }
            }
            $this->alterColumn('{{%news_revision}}', 'snapshot', $this->text()->notNull());
            $this->alterColumn('{{%news}}', 'content', $this->text()->notNull());
        }
        return true;
    }
}
