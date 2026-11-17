<?php

/** Shared team styles for frontend and backend pages. Maintainer: C. */

declare(strict_types=1);

namespace common\assets;

use yii\web\AssetBundle;

class TeamAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/team';
    public $css = ['team.css'];
    public $depends = ['yii\bootstrap5\BootstrapAsset'];
}
