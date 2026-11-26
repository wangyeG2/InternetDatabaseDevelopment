<?php

/** Publish the fixed team roster on fresh and existing deployments. */

declare(strict_types=1);

use yii\db\Migration;
use yii\db\Query;

class m261126_150000_seed_team_members extends Migration
{
    public function safeUp(): void
    {
        $now = time();
        $members = [
            [
                'legacy_slug' => 'example-member-a',
                'slug' => '2411294',
                'module_code' => 'A',
                'name' => '王烨',
                'role_title' => '组长',
                'duty' => '用户登录与管理，部署文档起草，设计文档起草，项目终审核查，PPT 编写与主讲',
                'sort_order' => 1,
            ],
            [
                'legacy_slug' => 'example-member-b',
                'slug' => '2411795',
                'module_code' => 'B',
                'name' => '阎科霖',
                'role_title' => '组员',
                'duty' => '新闻公告发布，实现文档起草，流程与模块图设计',
                'sort_order' => 2,
            ],
            [
                'legacy_slug' => 'example-member-c',
                'slug' => '2411738',
                'module_code' => 'C',
                'name' => '秦东杰',
                'role_title' => '组员',
                'duty' => '留言板及留言管理，需求文档起草，前端UI优化',
                'sort_order' => 3,
            ],
            [
                'legacy_slug' => 'example-member-d',
                'slug' => '2411020',
                'module_code' => 'D',
                'name' => '赵非凡',
                'role_title' => '组员',
                'duty' => '团队信息展示与编辑，用户手册起草',
                'sort_order' => 4,
            ],
        ];

        foreach ($members as $member) {
            $legacySlug = $member['legacy_slug'];
            unset($member['legacy_slug']);
            $attributes = $member + [
                'bio' => '煮啵很神秘~',
                'is_public' => 1,
                'updated_at' => $now,
            ];

            $existing = (new Query())
                ->select('id')
                ->from('{{%team_member}}')
                ->where(['slug' => $attributes['slug']])
                ->one($this->db);
            $legacy = (new Query())
                ->select('id')
                ->from('{{%team_member}}')
                ->where(['slug' => $legacySlug])
                ->one($this->db);
            $existingIdentity = (new Query())
                ->select('id')
                ->from('{{%team_member}}')
                ->where([
                    'module_code' => $attributes['module_code'],
                    'name' => $attributes['name'],
                ])
                ->andWhere(['<>', 'slug', $legacySlug])
                ->one($this->db);

            if ($existing !== false || $existingIdentity !== false) {
                if ($legacy !== false) {
                    $this->update('{{%team_member}}', ['is_public' => 0, 'updated_at' => $now], ['id' => $legacy['id']]);
                }
                continue;
            }

            if ($legacy !== false) {
                $this->update('{{%team_member}}', ['is_public' => 0, 'updated_at' => $now], ['id' => $legacy['id']]);
            }

            $this->insert('{{%team_member}}', $attributes + [
                'user_id' => null,
                'created_at' => $now,
            ]);
        }
    }

    public function safeDown(): bool
    {
        echo "Team roster initialization is retained to avoid deleting edited member data.\n";
        return false;
    }
}
