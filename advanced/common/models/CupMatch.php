<?php

/** Read-only tournament archive. Maintainer: 秦东杰. */

declare(strict_types=1);

namespace common\models;

use yii\db\Query;

final class CupMatch
{
    public static function fixtures(): Query
    {
        return (new Query())->from(['m' => '{{%cup_match}}'])
            ->innerJoin(['h' => '{{%cup_team}}'], 'h.code = m.home_code')
            ->innerJoin(['a' => '{{%cup_team}}'], 'a.code = m.away_code')
            ->select(['m.*', 'home' => 'h.name', 'away' => 'a.name']);
    }

    public static function score(array $match): array
    {
        $scores = json_decode($match['scores'], true, 512, JSON_THROW_ON_ERROR);
        $decisive = $scores['p'] ?? $scores['et'] ?? $scores['ft'];
        [$homeGoals, $awayGoals] = $scores['et'] ?? $scores['ft'];
        return [
            'home' => $homeGoals,
            'away' => $awayGoals,
            'winner' => $decisive[0] === $decisive[1] ? null : ($decisive[0] > $decisive[1] ? $match['home'] : $match['away']),
            'decider' => isset($scores['p']) ? '点球' : (isset($scores['et']) ? '加时' : ''),
            'line' => $homeGoals . ' : ' . $awayGoals,
            'note' => isset($scores['p']) ? '点球 ' . implode(' : ', $scores['p']) : (isset($scores['et']) ? '加时完场' : '全场'),
        ];
    }
}
