<?php

/** 2026 世界杯专题 · 前台本地资源。维护：秦东杰。 */

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class WorldCupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = ['css/arena/stats.css', 'css/kilnfield/faq.css', 'css/wc2026.css'];
    public $js = ['js/vendor/gsap.min.js', 'js/wc2026.js'];
    public $depends = [AppAsset::class];
}
