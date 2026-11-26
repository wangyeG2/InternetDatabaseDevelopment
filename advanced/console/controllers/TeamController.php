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
                ['2411294', 'A', '王烨', '组长', '用户登录与管理，部署文档起草，设计文档起草，项目终审核查，PPT 编写与主讲'],
                ['2411795', 'B', '阎科霖', '组员', '新闻公告发布，实现文档起草，流程与模块图设计'],
                ['2411738', 'C', '秦东杰', '组员', '留言板及留言管理，需求文档起草，前端UI优化'],
                ['2411020', 'D', '赵非凡', '组员', '团队信息展示与编辑，用户手册起草'],
            ];
            foreach ($members as $index => [$slug, $code, $name, $role, $duty]) {
                if (!TeamMember::find()->where(['slug' => $slug])
                    ->orWhere(['module_code' => $code, 'name' => $name])->exists()
                ) {
                    $this->insert(new TeamMember([
                        'name' => $name, 'slug' => $slug, 'module_code' => $code, 'role_title' => $role,
                        'duty' => $duty, 'bio' => '煮啵很神秘~',
                        'sort_order' => $index + 1, 'is_public' => 1,
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
                $this->insert(new TeamLink(['label' => 'GitHub 项目仓库', 'url' => $url, 'is_public' => 1, 'sort_order' => 1]));
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
