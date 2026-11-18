<?php

/** 世界杯专题 · 公开新闻只读接口，供小组复审。维护：秦东杰。 */

declare(strict_types=1);

namespace frontend\controllers;

use common\models\News;
use common\models\Message;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class DataController extends Controller
{
    public function actionIndex(): string
    {
        // Group Unix timestamps by UTC day independently of the MySQL session timezone.
        $day = new Expression('FLOOR(created_at / 86400)');
        $today = intdiv(time(), 86400);
        $days = array_fill_keys(range($today - 13, $today), 0);
        $rows = Message::published()->select(['d' => $day, 'n' => new Expression('COUNT(*)')])
            ->andWhere(['between', 'created_at', ($today - 13) * 86400, time()])
            ->groupBy($day)->asArray()->all();
        foreach ($rows as $row) {
            $days[(int) $row['d']] = (int) $row['n'];
        }
        $trend = [];
        foreach ($days as $d => $count) {
            $trend[gmdate('m-d', $d * 86400)] = $count;
        }
        $topics = [];
        foreach (Message::publishedTopicCounts() as $topic => $count) {
            $topics[Message::TOPICS[$topic]] = $count;
        }
        $series = [
            'trend' => $trend,
            'teams' => ['欧洲' => 16, '非洲' => 10, '亚洲' => 9, '中北美及加勒比' => 6, '南美' => 6, '大洋洲' => 1],
            'champions' => ['巴西' => 5, '德国' => 4, '意大利' => 4, '阿根廷' => 3,
                '法国' => 2, '乌拉圭' => 2, '英格兰' => 1, '西班牙' => 1],
            'topics' => $topics,
        ];
        return $this->render('index', ['series' => $series, 'chartJson' => Json::htmlEncode($series)]);
    }

    public function actionHeadlines(): Response
    {
        $rows = News::find()->publiclyVisible()->select(['n.id', 'n.title', 'n.summary'])
            ->orderBy(['n.published_at' => SORT_DESC, 'n.id' => SORT_DESC])->limit(5)->asArray()->all();
        foreach ($rows as &$row) {
            $row['url'] = Url::to(['/news/view', 'id' => $row['id']]);
        }
        unset($row);
        return $this->asJson($rows);
    }
}
