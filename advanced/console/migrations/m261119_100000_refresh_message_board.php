<?php

/** 升级 D 初版留言状态，加入无登录账号的展示样例。维护：秦东杰。 */

declare(strict_types=1);

use yii\db\Migration;

class m261119_100000_refresh_message_board extends Migration
{
    public function up(): void
    {
        $this->alterColumn('{{%message}}', 'author_id', $this->integer()->null());
        $this->db->transaction(function (): void {
            $this->update('{{%message}}', ['status' => 1], ['status' => 10]);
            $texts = [
                '从揭幕战开始记录这届世界杯。', '期待更多球队的赛后复盘。', '希望看到更多场馆的故事。',
                '编辑部的赛程速览很方便。', '想和大家交换观赛笔记。',
            ];
            foreach ($texts as $i => $text) {
                $this->insert('{{%message}}', [
                    'topic' => ['matches', 'matches', 'feedback', 'team', 'team'][$i],
                    'body' => $text, 'status' => 1, 'created_at' => 1781193600 + $i * 86400,
                ]);
            }
        });
    }

    public function down(): bool
    {
        return false;
    }
}
