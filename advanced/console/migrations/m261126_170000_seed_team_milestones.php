<?php

/** Publish the fixed project timeline while preserving existing entries. */

declare(strict_types=1);

use yii\db\Migration;
use yii\db\Query;

class m261126_170000_seed_team_milestones extends Migration
{
    public function safeUp(): void
    {
        $now = time();
        $milestones = [
            [
                'title' => '项目启动',
                'happened_on' => '2026-10-28',
                'summary' => '项目分工明确，主题及设计蓝图确定',
                'sort_order' => 1,
            ],
            [
                'title' => '项目推进',
                'happened_on' => '2026-10-31',
                'summary' => '基本框架搭建完成',
                'sort_order' => 2,
            ],
            [
                'title' => '项目推进',
                'happened_on' => '2026-11-07',
                'summary' => '项目前后端搭建基本完成',
                'sort_order' => 3,
            ],
            [
                'title' => '项目推进',
                'happened_on' => '2026-11-16',
                'summary' => '项目细节调整完成',
                'sort_order' => 4,
            ],
            [
                'title' => '项目完成',
                'happened_on' => '2026-11-19',
                'summary' => '项目所有内容验收通过预期',
                'sort_order' => 5,
            ],
        ];

        foreach ($milestones as $milestone) {
            $existing = (new Query())
                ->select('id')
                ->from('{{%team_milestone}}')
                ->where([
                    'title' => $milestone['title'],
                    'happened_on' => $milestone['happened_on'],
                ])
                ->one($this->db);
            if ($existing !== false) {
                continue;
            }

            $this->insert('{{%team_milestone}}', $milestone + [
                'is_example' => 0,
                'is_public' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function safeDown(): bool
    {
        echo "Timeline initialization is retained to avoid deleting edited milestone data.\n";
        return false;
    }
}
