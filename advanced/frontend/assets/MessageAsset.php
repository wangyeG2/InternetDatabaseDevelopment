<?php

/** Responsive styles and interactions for the message board. Maintainer: D (秦东杰). */

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

final class MessageAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = ['css/message.css'];
    public $js = ['js/message.js'];
    public $depends = [AppAsset::class];
}
