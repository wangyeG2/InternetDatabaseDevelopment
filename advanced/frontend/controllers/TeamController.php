<?php

/** Team module. Maintainer: C. */

declare(strict_types=1);

namespace frontend\controllers;

use common\models\TeamProfile;
use common\models\TeamMember;
use common\models\TeamMilestone;
use common\models\TeamLink;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeamController extends Controller
{
    public function behaviors(): array
    {
        return ['verbs' => ['class' => VerbFilter::class, 'actions' => ['index' => ['GET'], 'member' => ['GET']]]];
    }

    public function actionIndex(): string
    {
        return $this->render('index', [
            'profile' => TeamProfile::current(),
            'members' => TeamMember::find()->publiclyVisible()->ordered()->all(),
            'milestones' => TeamMilestone::find()->publiclyVisible()->ordered()->all(),
            'links' => TeamLink::find()->publiclyVisible()->ordered()->all(),
        ]);
    }

    public function actionMember(string $slug): string
    {
        $member = TeamMember::find()->publiclyVisible()->andWhere(['slug' => $slug])->with('duties')->one();
        if ($member === null) {
            throw new NotFoundHttpException('成员不存在或尚未公开。');
        }
        return $this->render('member', ['member' => $member]);
    }
}
