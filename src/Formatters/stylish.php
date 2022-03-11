<?php

namespace PHP\Project\Lvl2\Formatters\stylish;

function boolToStr(mixed $value): string
{
    return $value ? 'true' : 'false';
}

function getIndent(int $level, string $spaces = "    "): string
{
    return str_repeat($spaces, $level);
}

function arrayToStr(array $arr, int $level = 0): string
{
    $resultArr = array_reduce(
        array_keys($arr),
        function ($acc, $key) use ($arr, $level) {
            $indent = getIndent($level);

            if (!is_array($arr[$key])) {
                $outputChild = ["{$indent}    {$key}: {$arr[$key]}"];
                return array_merge($acc, $outputChild);
            }
            $output = ["{$indent}    {$key}: {\n" . arrayToStr($arr[$key], ++$level) . "\n{$indent}    }"];
            return array_merge($acc, $output);
        },
        []
    );

    return implode("\n", $resultArr);
}

function getValue(mixed $value, int $level)
{
    $boolToStrValue = is_bool($value) ? boolToStr($value) : $value;
    $indent = getIndent($level);
    $arrayToStrValue = is_array($value) ? "{\n" . arrayToStr($value, ++$level) . "\n    {$indent}}" : $boolToStrValue;
    $nullToStr = is_null($value) ? 'null' : $arrayToStrValue;

    return $nullToStr;
}

function makeOutputArray(array $diff, int $level = 0): array
{

    return array_map(function ($item) use ($level) {
        $key = $item['key'];
        $value = $item['value'];
        $notParentValue = getValue($value, $level);
        $type = $item['type'];

        $indent = getIndent($level);

        if ($type === 'added') {
            return "{$indent}  + {$key}: {$notParentValue}";
        } elseif ($type === 'removed') {
            return "{$indent}  - {$key}: {$notParentValue}";
        } elseif ($type === 'same') {
            return "{$indent}    {$key}: {$notParentValue}";
        } elseif ($type === 'changed') {
            $old = 0;
            $new = 1;
            $oldValue = getValue($value[$old], $level);
            $newValue = getValue($value[$new], $level);
            return "{$indent}  - {$key}: {$oldValue}\n{$indent}  + {$key}: {$newValue}";
        } elseif ($type === 'parent') {
            $resArr = makeOutputArray($value, ++$level);
            $value = implode(PHP_EOL, $resArr);
            $minIndent = getIndent($level, "  ");
            return "{$indent}    {$key}: {\n{$value}\n    {$indent}}";
        }
    }, $diff);
}

function formatStylish(array $diff)
{
    if (count($diff) === 0) {
        return '';
    }

    $outputArr = makeOutputArray($diff);
    $output = implode(PHP_EOL, $outputArr);

    return "{\n{$output}\n}";
}
