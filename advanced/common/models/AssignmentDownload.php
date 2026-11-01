<?php

/** Only ZIP files explicitly placed in the coursework download directories are public. Maintainer: B. */

declare(strict_types=1);

namespace common\models;

class AssignmentDownload
{
    public static function groups(): array
    {
        return ['team' => '团队作业', 'personal' => '个人作业'];
    }

    public static function available(string $group): array
    {
        if (!isset(self::groups()[$group])) {
            return [];
        }
        $directory = dirname(__DIR__, 3) . '/data/' . $group;
        $root = realpath($directory);
        if ($root === false) {
            return [];
        }
        $items = [];
        foreach (glob($directory . '/*.zip') ?: [] as $candidate) {
            $path = realpath($candidate);
            if (
                $path === false || !is_file($path) || is_link($candidate)
                || strcasecmp(dirname($path), $root) !== 0
            ) {
                continue;
            }
            $name = basename($path);
            $items[] = ['id' => hash('sha256', $group . ':' . $name), 'name' => $name,
                'path' => $path, 'bytes' => filesize($path), 'group' => $group];
        }
        usort($items, static fn (array $first, array $second): int => strcmp($first['name'], $second['name']));
        return $items;
    }

    public static function find(string $group, string $id): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/D', $id)) {
            return null;
        }
        foreach (self::available($group) as $item) {
            if (hash_equals($item['id'], $id)) {
                return $item;
            }
        }
        return null;
    }
}
