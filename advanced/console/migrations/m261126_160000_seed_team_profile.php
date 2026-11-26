<?php

/** Publish the fixed team profile when no custom current profile exists. */

declare(strict_types=1);

use yii\db\Migration;
use yii\db\Query;

class m261126_160000_seed_team_profile extends Migration
{
    public function safeUp(): void
    {
        $slug = 'chicken-king-dumpling-team';
        $now = time();
        $attributes = [
            'name' => 'ChickenKingDumpling队',
            'slug' => $slug,
            'motto' => 'make cs great again!',
            'summary' => '这是由南开大学计算机学院2024级的四位同学组成的互联网数据库大作业项目团队',
            'body' => '我们是一支四人小组，因为对足球和代码共同的热爱走到一起。2026年世界杯即将在美加墨三国点燃战火，48支球队、104场比赛，规模空前。我们希望用一学期所学的数据库知识，为球迷搭建一座资讯与数据兼备的新闻门户——"绿茵前线"，让每一场比赛、每一条新闻、每一条留言，都能被妥善地记录、检索与阅读。',
            'theme_note' => '',
            'is_current' => 1,
            'updated_by' => null,
            'updated_at' => $now,
        ];

        $current = (new Query())
            ->select(['id', 'slug', 'name'])
            ->from('{{%team_profile}}')
            ->where(['is_current' => 1])
            ->one($this->db);
        if ($current !== false) {
            if ($current['slug'] === 'example-team' && $current['name'] === '示例 · 世界杯课程团队') {
                $this->update('{{%team_profile}}', $attributes, ['id' => $current['id']]);
            }
            return;
        }

        $existing = (new Query())
            ->select('id')
            ->from('{{%team_profile}}')
            ->where(['slug' => $slug])
            ->one($this->db);
        if ($existing !== false) {
            $this->update('{{%team_profile}}', ['is_current' => 1, 'updated_at' => $now], ['id' => $existing['id']]);
            return;
        }

        $legacy = (new Query())
            ->select(['id', 'name'])
            ->from('{{%team_profile}}')
            ->where(['slug' => 'example-team'])
            ->one($this->db);
        if ($legacy !== false && $legacy['name'] === '示例 · 世界杯课程团队') {
            $this->update('{{%team_profile}}', $attributes, ['id' => $legacy['id']]);
            return;
        }

        $this->insert('{{%team_profile}}', $attributes + [
            'created_at' => $now,
        ]);
    }

    public function safeDown(): bool
    {
        echo "Team profile initialization is retained to avoid deleting edited profile data.\n";
        return false;
    }
}
