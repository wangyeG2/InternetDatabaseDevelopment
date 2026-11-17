<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace backend\controllers;

use backend\models\TeamProfileForm;
use common\models\TeamProfile;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class TeamProfileController extends Controller
{
    public function behaviors(): array
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => [
            'index' => ['GET'], 'update' => ['GET', 'POST'],
        ]]];
    }

    public function actionIndex(): string
    {
        return $this->render('index', ['dataProvider' => new ActiveDataProvider([
            'query' => TeamProfile::find()->orderBy(['id' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ])]);
    }

    public function actionUpdate(): string|Response
    {
        $record = TeamProfile::current() ?? new TeamProfile();
        return $this->edit(TeamProfileForm::forRecord($record));
    }

    private function edit(TeamProfileForm $form): string|Response
    {
        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', '团队数据已保存。');
            return $this->redirect(['index']);
        }
        return $this->render('edit', ['model' => $form]);
    }
}
