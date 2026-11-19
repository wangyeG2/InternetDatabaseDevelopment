<?php

/** Public message board and authenticated submissions. Maintainer: D (秦东杰). */

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Message;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

final class MessageController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create'],
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['index' => ['GET', 'HEAD'], 'create' => ['POST']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->renderBoard(new Message());
    }

    public function actionCreate(): string|Response
    {
        $message = new Message();
        $loaded = $message->load(Yii::$app->request->post());
        if (Yii::$app->request->isAjax && Yii::$app->request->post('ajax') === 'message-form') {
            return $this->asJson(ActiveForm::validate($message));
        }
        if ($loaded && $message->submit((int) Yii::$app->user->id)) {
            Yii::$app->session->setFlash('success', '留言已提交，审核通过后上墙。');
            return $this->redirect(['index']);
        }

        return $this->renderBoard($message);
    }

    private function renderBoard(Message $message): string
    {
        return $this->render('index', [
            'model' => $message,
            'messages' => new ActiveDataProvider([
                'query' => Message::published()->with('author')->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]),
                'pagination' => ['pageSize' => 10],
                'sort' => false,
            ]),
            'topicCounts' => Message::publishedTopicCounts(),
        ]);
    }
}
