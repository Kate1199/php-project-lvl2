<?php

namespace Differ\Differ;

use function Functional\flatten;
use function PHP\Project\Lvl2\Parsers\makeAssociativeArray;
use function PHP\Project\Lvl2\Formatters\formatChooser;

function getDiffByKey(array $file1, array $file2, mixed $key): array
{
    if (!array_key_exists($key, $file1) && !array_key_exists($key, $file2)) {
        return [];
    }

    if (!array_key_exists($key, $file1)) {
        return ['type' => 'added', 'key' => $key, 'value' => $file2[$key]];
    } elseif (!array_key_exists($key, $file2)) {
        return ['type' => 'removed', 'key' => $key, 'value' => $file1[$key]];
    } elseif ($file1[$key] === $file2[$key]) {
        return ['type' => 'same', 'key' => $key, 'value' => $file1[$key]];
    } elseif (isAssoc($file1[$key]) && isAssoc($file2[$key])) {
        return ['type' => 'parent', 'key' => $key, 'value' => getChildrenDiff($file1[$key], $file2[$key])];
    } elseif ($file1[$key] !== $file2[$key]) {
        return ['type' => 'changed', 'key' => $key, 'value' => [$file1[$key], $file2[$key]]];
    }

    return [];
}

function isAssoc(mixed $content): bool
{
    if (!is_array($content)) {
        return false;
    }

    $length = count($content);
    return array_keys($content) !== range(0, $length - 1);
}

function getChildrenDiff(mixed $file1, mixed $file2): array
{
    $keys = array_merge(
        array_keys($file1),
        array_keys($file2)
    );
    $uniqueKeys = array_unique($keys);
    $sortedKeys = collect($uniqueKeys)->sort()->toArray(); // @phpstan-ignore-line

    if (count($sortedKeys) === 0) {
        return [];
    }

    return array_reduce($sortedKeys, function ($acc, $key) use ($file1, $file2) {
        $diff = [getDiffByKey($file1, $file2, $key)];
        return array_merge($acc, $diff);
    }, []);
}

function genDiff(string $filename1, string $filename2, string $format = 'stylish'): string
{
    $file1 = makeAssociativeArray($filename1);
    $file2 = makeAssociativeArray($filename2);

    $outputArr = getChildrenDiff($file1, $file2);

    return formatChooser($format, $outputArr);
}
