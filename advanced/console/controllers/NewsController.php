<?php

/** Opt-in, repeatable sample content for the news flow. Maintainer: B. */
declare(strict_types=1);

namespace console\controllers;

use backend\models\NewsForm;
use backend\services\NewsService;
use common\models\News;
use common\models\NewsCategory;
use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class NewsController extends Controller
{
    /** Seed clearly labelled sample posts; requires an explicitly provisioned administrator. */
    public function actionSeed(string $username): int
    {
        $admin = User::findByUsername($username);
        if ($admin === null || !$admin->isAdmin()) {
            $this->stderr("An active administrator is required. Use access/grant-admin first.\n");
            return ExitCode::DATAERR;
        }
        $category = NewsCategory::find()->where(['is_active' => 1])->orderBy(['sort_order' => SORT_ASC])->one();
        if ($category === null) {
            $this->stderr("Create an active news category first.\n");
            return ExitCode::DATAERR;
        }
        $samples = [
            ['sample-team-notice', '示例 团队观赛交流活动通知', '团队活动安排与参与说明。',
                "本条为课程项目的示例公告，不代表真实活动安排。\n\n团队计划围绕世界杯主题开展观赛交流。活动时间、地点和报名方式由管理员在确认后更新。", '团队,活动', '10', '1'],
            ['sample-project-update', '示例 团队主页新闻模块上线', '新闻浏览、分类筛选和后台发布功能已接入。',
                "本条为课程项目的示例内容。\n\n管理员可在后台创建新闻，保存草稿后再发布。普通访客可在前台浏览已经发布的内容。", '项目,公告', '10', '0'],
            ['sample-draft', '示例 待完善的活动草稿', '这条草稿仅在后台显示。',
                '待补充具体安排后，再将草稿发布。', '草稿', '0', '0'],
        ];
        foreach ($samples as [$slug, $title, $summary, $content, $tags, $status, $pinned]) {
            if (News::find()->where(['slug' => $slug])->exists()) {
                continue;
            }
            $form = new NewsForm(['slug' => $slug, 'title' => $title, 'summary' => $summary, 'content' => $content,
                'tags' => $tags, 'status' => $status, 'is_pinned' => $pinned, 'category_id' => (string) $category->id]);
            if ((new NewsService())->save($form, $admin) === null) {
                $this->stderr("Sample content could not be saved.\n");
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }
        $this->stdout("Sample news is ready. Existing posts were preserved.\n");
        return ExitCode::OK;
    }
}
