<?php

/** Coursework download entry and allowlisted file responses. Maintainer: B. */

declare(strict_types=1);

namespace frontend\controllers;

use common\models\AssignmentDownload;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DownloadController extends Controller
{
    public function behaviors(): array
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['file' => ['GET', 'HEAD']]]];
    }

    public function actionIndex(): string
    {
        $groups = [];
        foreach (AssignmentDownload::groups() as $group => $label) {
            $groups[] = ['label' => $label, 'items' => AssignmentDownload::available($group)];
        }
        return $this->render('index', ['groups' => $groups]);
    }

    public function actionFile(string $group, string $id): Response
    {
        $item = AssignmentDownload::find($group, $id);
        if ($item === null) {
            throw new NotFoundHttpException('下载文件不存在。');
        }
        return Yii::$app->response->sendFile($item['path'], $item['name'], ['mimeType' => 'application/zip', 'inline' => false]);
    }
}
