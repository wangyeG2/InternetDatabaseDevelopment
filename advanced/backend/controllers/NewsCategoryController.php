<?php

/** Category list, creation and editing; used only by administrators. Maintainer: B. */
declare(strict_types=1);

namespace backend\controllers;

use backend\models\NewsCategoryForm;
use common\models\NewsCategory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NewsCategoryController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index', ['dataProvider' => new ActiveDataProvider([
            'query' => NewsCategory::find()->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ])]);
    }

    public function actionCreate(): string|Response
    {
        return $this->edit(new NewsCategoryForm());
    }

    public function actionUpdate(int $id): string|Response
    {
        $category = NewsCategory::findOne($id);
        if ($category === null) {
            throw new NotFoundHttpException('分类不存在。');
        }
        return $this->edit(NewsCategoryForm::forRecord($category));
    }

    private function edit(NewsCategoryForm $form): string|Response
    {
        if ($form->load(Yii::$app->request->post()) && $form->save() !== null) {
            Yii::$app->session->setFlash('success', '分类已保存。');
            return $this->redirect(['index']);
        }
        return $this->render('edit', ['model' => $form]);
    }
}
