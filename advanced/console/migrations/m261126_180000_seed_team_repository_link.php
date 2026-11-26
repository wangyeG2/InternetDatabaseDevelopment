<?php

/** Publish the project repository link without duplicating existing entries. */

declare(strict_types=1);

use yii\db\Migration;
use yii\db\Query;

class m261126_180000_seed_team_repository_link extends Migration
{
    public function safeUp(): void
    {
        $url = 'https://github.com/wangyeG2/InternetDatabaseDevelopment';
        $existing = (new Query())
            ->select(['id', 'label'])
            ->from('{{%team_link}}')
            ->where(['url' => $url])
            ->one($this->db);

        if ($existing !== false) {
            $updates = [
                'is_public' => 1,
                'updated_at' => time(),
            ];
            if ($existing['label'] === '示例：项目源码仓库') {
                $updates['label'] = 'GitHub 项目仓库';
            }
            $this->update('{{%team_link}}', $updates, ['id' => $existing['id']]);
            return;
        }

        $now = time();
        $this->insert('{{%team_link}}', [
            'label' => 'GitHub 项目仓库',
            'url' => $url,
            'sort_order' => 1,
            'is_public' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function safeDown(): bool
    {
        echo "Repository link initialization is retained to avoid deleting edited link data.\n";
        return false;
    }
}
