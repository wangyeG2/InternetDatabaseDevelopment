<?php

/** Administrator account management. Maintainer: A. */

declare(strict_types=1);

namespace backend\controllers;

use backend\models\UserManagementForm;
use backend\models\UserSearch;
use common\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class UserController extends Controller
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'update' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $search = new UserSearch();
        $dataProvider = $search->search(Yii::$app->request->get());

        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $user = User::findOne($id);
        if ($user === null) {
            throw new NotFoundHttpException('用户不存在。');
        }

        $form = UserManagementForm::forRecord($user);
        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', '用户权限与状态已保存。');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $form]);
    }
}
