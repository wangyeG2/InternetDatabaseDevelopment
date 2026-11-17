<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace console\controllers;

use common\models\TeamProfile;
use common\models\TeamMember;
use common\models\TeamMilestone;
use common\models\TeamLink;
use RuntimeException;
use Yii;
use yii\console\Controller;
use yii\db\ActiveRecord;

class TeamController extends Controller
{
    public function actionSeed(): int
    {
        Yii::$app->db->transaction(function (): void {
            if (TeamProfile::current() === null && !TeamProfile::find()->where(['slug' => 'example-team'])->exists()) {
                $this->insert(new TeamProfile([
                    'name' => '示例 · 世界杯课程团队', 'slug' => 'example-team', 'motto' => '示例：协作、记录、展示',
                    'summary' => '示例团队介绍，请管理员按实际进度编辑。', 'body' => '示例：这是2026世界杯主题课程项目。',
                    'theme_note' => '示例：采用足球编辑版式，不提供虚构赛事结果。', 'is_current' => 1,
                ]));
            }
            $members = [
                ['A', '王烨', '账号与部署', '账号模块；部署材料待组长汇总'],
                ['B', '阎科霖', '新闻公告', '新闻公告与作业下载（已集成）'],
                ['C', '赵非凡', '团队展示与布局', '团队展示和编辑MVC、共享导航、视图约定、用户手册'],
                ['D', '秦东杰', '留言与动态图形', '留言板和动态图形（待集成）'],
            ];
            foreach ($members as $index => [$code, $name, $role, $duty]) {
                $slug = 'example-member-' . strtolower($code);
                if (!TeamMember::find()->where(['slug' => $slug])->exists()) {
                    $this->insert(new TeamMember([
                        'name' => $name, 'slug' => $slug, 'module_code' => $code, 'role_title' => $role,
                        'duty' => $duty, 'bio' => '示例介绍：请本人补充个人展示内容。',
                        'sort_order' => $index, 'is_public' => 1,
                    ]));
                }
            }
            foreach ([['2026-11-28', '示例计划：完成课程项目'], ['2026-12-01', '示例计划：提交课程材料']] as [$date, $title]) {
                if (!TeamMilestone::find()->where(['title' => $title, 'happened_on' => $date])->exists()) {
                    $this->insert(new TeamMilestone([
                        'title' => $title, 'happened_on' => $date, 'summary' => '示例项目计划，不表示已经完成。',
                        'is_example' => 1, 'is_public' => 1, 'sort_order' => 0,
                    ]));
                }
            }
            $url = 'https://github.com/wangyeG2/InternetDatabaseDevelopment';
            if (!TeamLink::find()->where(['url' => $url])->exists()) {
                $this->insert(new TeamLink(['label' => '示例：项目源码仓库', 'url' => $url, 'is_public' => 1, 'sort_order' => 0]));
            }
        });
        $this->stdout("示例数据已补充；同一标识的已有资料保持不变。未创建账号。\n");
        return self::EXIT_CODE_NORMAL;
    }

    private function insert(ActiveRecord $record): void
    {
        if (!$record->save()) {
            throw new RuntimeException('示例数据验证失败：' . json_encode($record->errors, JSON_UNESCAPED_UNICODE));
        }
    }
}
