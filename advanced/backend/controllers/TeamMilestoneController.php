<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\controllers;

use backend\models\TeamMilestoneForm;
use common\models\TeamMilestone;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TeamMilestoneController extends Controller
{
    public function behaviors(): array
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['hide' => ['POST'],
            'index' => ['GET'], 'create' => ['GET', 'POST'], 'update' => ['GET', 'POST']]]];
    }

    public function actionIndex(): string
    {
        return $this->render('index', ['dataProvider' => new ActiveDataProvider([
            'query' => TeamMilestone::find()->orderBy(['id' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ])]);
    }

    public function actionCreate(): string|Response
    {
        return $this->edit(new TeamMilestoneForm());
    }

    public function actionUpdate(int $id): string|Response
    {
        $record = TeamMilestone::findOne($id);
        if ($record === null) {
            throw new NotFoundHttpException('记录不存在。');
        }
        return $this->edit(TeamMilestoneForm::forRecord($record));
    }

    public function actionHide(int $id): Response
    {
        $record = TeamMilestone::findOne($id);
        if ($record === null) {
            throw new NotFoundHttpException('记录不存在。');
        }
        $record->is_public = 0;
        $record->save(false, ['is_public', 'updated_at']);
        Yii::$app->session->setFlash('success', '已隐藏，数据保留。');
        return $this->redirect(['index']);
    }

    private function edit(TeamMilestoneForm $form): string|Response
    {
        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', '团队数据已保存。');
            return $this->redirect(['index']);
        }
        return $this->render('edit', ['model' => $form]);
    }
}
