<?php

/** Compose the sports desk without changing the team homepage controller. Maintainer: 秦东杰. */

declare(strict_types=1);

namespace frontend\widgets;

use common\models\CupMatch;
use common\models\News;
use yii\base\Widget;

final class TournamentEdition extends Widget
{
    public function run(): string
    {
        $latest = News::find()->publiclyVisible()->orderBy(['n.published_at' => SORT_DESC, 'n.id' => SORT_DESC])
            ->limit(5)->all();
        return $this->view->render('@frontend/views/site/_edition', [
            'news' => $latest,
            'opening' => CupMatch::fixtures()->orderBy(['m.kickoff' => SORT_ASC, 'm.id' => SORT_ASC])->limit(4)->all(),
            'finals' => CupMatch::fixtures()->orderBy(['m.kickoff' => SORT_DESC, 'm.id' => SORT_DESC])->limit(3)->all(),
            'quarter' => CupMatch::fixtures()->where(['m.stage' => '四分之一决赛'])->orderBy('m.kickoff')->all(),
        ]);
    }
}
