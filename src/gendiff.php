<?php

namespace Differ\Differ;

use function PHP\Project\Lvl2\Parsers\parseFIle;
use function PHP\Project\Lvl2\Formatters\formatChooser;

function getDiffByKey(array $file1, array $file2, mixed $key): array
{
    if (!isset($file1[$key]) && !isset($file2[$key])) {
        return [];
    }

    if (!isset($file1[$key])) {
        return ['type' => 'added', 'key' => $key, 'value' => $file2[$key]];
    } elseif (!isset($file2[$key])) {
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

function getChildrenDiff(array $file1, array $file2): array
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
    $fileArr1 = parseFile($filename1);
    $fileArr2 = parseFile($filename2);

    $outputArr = getChildrenDiff($fileArr1, $fileArr2);

    return formatChooser($format, $outputArr);
}
