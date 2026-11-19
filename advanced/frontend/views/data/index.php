<?php

/** 数据版，图表使用本地 ECharts，逐项保留可访问的表格。维护：秦东杰。 */

declare(strict_types=1);

use frontend\assets\WorldCupAsset;
use yii\helpers\Html;

$this->title = '世界杯数据版';
$this->registerJsFile('@web/js/vendor/echarts.min.js', ['depends' => WorldCupAsset::class], 'wc-echarts');
$this->registerJsFile('@web/js/wc-charts.js', ['depends' => WorldCupAsset::class], 'wc-charts');
$titles = ['trend' => '近十四天公开留言', 'teams' => '48 强的足联分布', 'champions' => '历届冠军次数', 'topics' => '全部公开留言的话题'];
$notes = ['trend' => '按提交日期统计当前已公开的留言，使用 UTC 日期。',
    'teams' => '2026 年参赛名单，按所属足联统计。', 'champions' => '1930–2022 年，德国含联邦德国。',
    'topics' => '统计包含标注为编辑部示例的公开留言。'];
?>
<article class="wc-data"><div class="wc-kicker"><i></i> DATA DESK</div><h1>世界杯数据版</h1>
<div class="wc-data-grid">
    <?php foreach ($titles as $key => $title): ?>
        <section><h2><?= Html::encode($title) ?></h2><p><?= Html::encode($notes[$key]) ?></p>
            <?php if (array_sum($series[$key]) === 0): ?><p class="wc-empty">暂无数据</p><?php else: ?>
                <div class="wc-chart" data-wc-chart="<?= $key ?>" aria-hidden="true"></div>
            <?php endif ?>
            <details><summary>查看数据表</summary><table>
                <?php foreach ($series[$key] as $name => $value): ?><tr><th scope="row"><?= Html::encode($name) ?></th><td><?= (int) $value ?></td></tr><?php endforeach ?>
            </table></details>
        </section>
    <?php endforeach ?>
</div>
<script type="application/json" id="wc-chart-data"><?= $chartJson ?></script>
</article>
