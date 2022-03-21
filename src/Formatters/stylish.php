<?php

namespace PHP\Project\Lvl2\Formatters\stylish;

function boolToStr(bool $value): string
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
            $childArray = arrayToStr($arr[$key], $level + 1);
            $output = ["{$indent}    {$key}: {\n{$childArray}\n{$indent}    }"];
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
    $arrayToStrValue = is_array($value) ? "{\n" . arrayToStr($value, $level + 1) . "\n    {$indent}}" : $boolToStrValue;
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

        switch ($item['type']) {
            case 'added':
                return sprintf("%s  + %s: %s", $indent, $key, $notParentValue);
            case 'removed':
                return sprintf("%s  - %s: %s", $indent, $key, $notParentValue);
            case 'same':
                return sprintf("%s    %s: %s", $indent, $key, $notParentValue);
            case 'changed':
                $oldValue = getValue($value[0], $level);
                $newValue = getValue($value[1], $level);
                return sprintf("%s  - %s: %s\n%s  + %s: %s", $indent, $key, $oldValue, $indent, $key, $newValue);
            case 'parent':
                $resArr = makeOutputArray($value, $level + 1);
                $output = implode("\n", $resArr);
                $minIndent = getIndent($level, "  ");
                return sprintf("%s    %s: {\n%s\n    %s}", $indent, $key, $output, $indent);
        }
    }, $diff);
}

function formatStylish(array $diff)
{
    if (count($diff) === 0) {
        return '';
    }

    $outputArr = makeOutputArray($diff);
    $output = implode("\n", $outputArr);

    return sprintf("{\n%s\n}", $output);
}
