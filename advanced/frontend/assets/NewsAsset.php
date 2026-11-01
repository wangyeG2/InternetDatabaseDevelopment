<?php

/** Styles scoped to the public news pages. Maintainer: B. */

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class NewsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = ['css/news.css'];
    public $depends = [AppAsset::class];
}
