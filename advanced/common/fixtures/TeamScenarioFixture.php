<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace common\fixtures;

use RuntimeException;
use Yii;
use yii\test\Fixture;

class TeamScenarioFixture extends Fixture
{
    public $depends = [NewsScenarioFixture::class];

    public function load(): void
    {
        $this->clear();
        $now = time();
        Yii::$app->db->createCommand()->insert('{{%team_profile}}', [
            'id' => 1, 'slug' => 'fixture-team', 'name' => '示例测试团队', 'body' => '团队测试正文',
            'is_current' => 1, 'created_at' => $now, 'updated_at' => $now,
        ])->execute();
        foreach ([[1, 'public-member', 1], [2, 'hidden-member', 0]] as [$id, $slug, $public]) {
            Yii::$app->db->createCommand()->insert('{{%team_member}}', [
                'id' => $id, 'name' => '示例成员' . $id, 'slug' => $slug, 'bio' => '示例个人介绍',
                'is_public' => $public, 'sort_order' => $id, 'created_at' => $now, 'updated_at' => $now,
            ])->execute();
        }
        Yii::$app->db->createCommand()->insert('{{%team_member_duty}}', [
            'member_id' => 1, 'title' => '示例职责', 'detail' => '示例职责说明',
        ])->execute();
        foreach ([0, 1] as $visible) {
            Yii::$app->db->createCommand()->insert('{{%team_milestone}}', [
                'title' => '示例节点' . $visible, 'happened_on' => '2026-11-28', 'is_example' => 1,
                'is_public' => $visible, 'created_at' => $now, 'updated_at' => $now,
            ])->execute();
            Yii::$app->db->createCommand()->insert('{{%team_link}}', [
                'label' => '示例链接' . $visible, 'url' => 'https://example.test/',
                'is_public' => $visible, 'created_at' => $now, 'updated_at' => $now,
            ])->execute();
        }
    }

    public function unload(): void
    {
        $this->clear();
    }

    private function clear(): void
    {
        if (!YII_ENV_TEST || !str_contains(Yii::$app->db->dsn, 'test')) {
            throw new RuntimeException('TeamScenarioFixture requires a dedicated test database.');
        }
        foreach (['team_member_duty', 'team_member', 'team_profile', 'team_milestone', 'team_link'] as $table) {
            Yii::$app->db->createCommand()->delete('{{%' . $table . '}}')->execute();
        }
    }
}
