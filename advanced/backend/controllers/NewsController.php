<?php

/** Management news routes; protected by the application admin guard. Maintainer: B. */
declare(strict_types=1);

namespace backend\controllers;

use backend\models\NewsForm;
use backend\services\NewsService;
use common\models\News;
use common\models\NewsCategory;
use common\models\NewsSearch;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NewsController extends Controller
{
    public function behaviors(): array
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => [
            'publish' => ['POST'], 'withdraw' => ['POST'], 'delete' => ['POST'],
        ]]];
    }

    public function actionIndex(): string
    {
        $search = new NewsSearch();
        return $this->render('index', [
            'search' => $search, 'dataProvider' => $search->search(Yii::$app->request->queryParams, true),
            'categories' => NewsCategory::options(false),
        ]);
    }

    public function actionView(int $id): string
    {
        $news = $this->findNews($id, true);
        return $this->render('view', [
            'news' => $news,
            'history' => new ActiveDataProvider(['query' => $news->getRevisions(), 'pagination' => ['pageSize' => 10]]),
        ]);
    }

    public function actionCreate(): string|Response
    {
        $form = new NewsForm();
        if ($form->load(Yii::$app->request->post())) {
            $news = (new NewsService())->save($form, $this->editor());
            if ($news !== null) {
                Yii::$app->session->setFlash('success', '新闻已保存。');
                return $this->redirect(['view', 'id' => $news->id]);
            }
        }
        return $this->render('create', ['model' => $form, 'categories' => NewsCategory::options(false)]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $form = NewsForm::forRecord($this->findNews($id));
        if ($form->load(Yii::$app->request->post()) && (new NewsService())->save($form, $this->editor()) !== null) {
            Yii::$app->session->setFlash('success', '新闻修改已保存。');
            return $this->redirect(['view', 'id' => $id]);
        }
        return $this->render('update', ['model' => $form, 'categories' => NewsCategory::options(false)]);
    }

    public function actionPublish(int $id): Response
    {
        (new NewsService())->publish($this->findNews($id), $this->editor());
        Yii::$app->session->setFlash('success', '新闻已发布。');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionWithdraw(int $id): Response
    {
        (new NewsService())->withdraw($this->findNews($id), $this->editor());
        Yii::$app->session->setFlash('success', '新闻已撤回为草稿。');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete(int $id): Response
    {
        (new NewsService())->delete($this->findNews($id), $this->editor());
        Yii::$app->session->setFlash('success', '新闻已删除，修改记录仍保留。');
        return $this->redirect(['index']);
    }

    private function findNews(int $id, bool $includeDeleted = false): News
    {
        $query = News::find()->with(['category', 'author', 'tags'])->andWhere(['n.id' => $id]);
        if (!$includeDeleted) {
            $query->andWhere(['n.deleted_at' => null]);
        }
        $news = $query->one();
        if ($news === null) {
            throw new NotFoundHttpException('新闻不存在。');
        }
        return $news;
    }

    private function editor(): User
    {
        return Yii::$app->user->identity;
    }
}
