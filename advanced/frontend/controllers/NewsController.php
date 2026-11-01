<?php

/** Public news list and detail routes. Maintainer: B. */

declare(strict_types=1);

namespace frontend\controllers;

use common\models\News;
use common\models\NewsCategory;
use common\models\NewsSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NewsController extends Controller
{
    public function actionIndex(): string
    {
        $search = new NewsSearch();
        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $search->search(Yii::$app->request->queryParams),
            'categories' => NewsCategory::options(),
        ]);
    }

    public function actionView(int $id): string
    {
        $news = News::find()->publiclyVisible()->with(['category', 'author', 'tags'])->andWhere(['n.id' => $id])->one();
        if (!$news instanceof News) {
            throw new NotFoundHttpException('这篇新闻不存在或尚未发布。');
        }
        return $this->render('view', ['news' => $news]);
    }
}
