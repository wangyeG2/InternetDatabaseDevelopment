<?php

/** Administrator message moderation. Maintainer: D (秦东杰). */

declare(strict_types=1);

namespace backend\controllers;

use common\models\Message;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class MessageController extends Controller
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['index' => ['GET', 'HEAD'], 'publish' => ['POST'], 'hide' => ['POST']],
            ],
        ];
    }

    public function actionIndex(string $status = 'pending'): string
    {
        $query = Message::find()->with('author')->orderBy(['id' => SORT_DESC]);
        if ($status !== 'all') {
            if (!isset(Message::STATUSES[$status])) {
                throw new BadRequestHttpException('留言状态无效。');
            }
            $query->andWhere(['status' => Message::STATUSES[$status]]);
        }

        return $this->render('index', [
            'filter' => $status,
            'messages' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['pageSize' => 15],
                'sort' => false,
            ]),
        ]);
    }

    public function actionPublish(int $id, string $filter = 'pending'): Response
    {
        return $this->moderate($id, Message::PUBLISHED, $filter);
    }

    public function actionHide(int $id, string $filter = 'pending'): Response
    {
        return $this->moderate($id, Message::HIDDEN, $filter);
    }

    private function moderate(int $id, int $status, string $filter): Response
    {
        $message = Message::findOne($id);
        if ($message === null) {
            throw new NotFoundHttpException('这条留言不存在。');
        }

        $original = Yii::$app->request->post('originalStatus');
        if (!in_array($original, array_map('strval', Message::STATUSES), true)) {
            throw new BadRequestHttpException('缺少有效的留言原状态。');
        }

        if ($message->moderate($status, (int) Yii::$app->user->id, (int) $original)) {
            Yii::$app->session->setFlash('success', '留言状态已更新。');
        } else {
            Yii::$app->session->setFlash('warning', '留言状态已变更，请确认当前状态后再操作。');
        }

        return $this->redirect(['index', 'status' => isset(Message::STATUSES[$filter]) || $filter === 'all' ? $filter : 'pending']);
    }
}
