<?php

namespace PHP\Project\Lvl2\stylish;

function boolToStr($value): string
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
                $acc[] = "{$indent}  {$key}: {$arr[$key]}";
                return $acc;
            }

            $acc[] = "{$indent}  {$key}: {\n" . arrayToStr($arr[$key], ++$level) . "\n{$indent}  }";
            return $acc;
        },
    );

    return implode("\n", $resultArr);
}

function getValue($value, int $level)
{
    $boolToStrValue = is_bool($value) ? boolToStr($value) : $value;
    $indent = getIndent($level);
    $arrayToStrValue = is_array($value) ? "{\n" . arrayToStr($value, ++$level) . "\n  {$indent}}" : $boolToStrValue;
    $nullToStr = is_null($value) ? 'null' : $arrayToStrValue;

    return $nullToStr;
}

function makeOutputArray(array $diff, int $level = 0): array
{

    return array_map(function ($item) use ($level) {
        $key = $item['key'];
        $value = $item['value'];
        $notParentValue = getValue($value, $level);

        $indent = getIndent($level);

        $resultLine = '';
        switch ($item['type']) {
            case 'added':
                $resultLine = "{$indent}+ {$key}: {$notParentValue}";
                break;
            case 'removed':
                $resultLine = "{$indent}- {$key}: {$notParentValue}";
                break;
            case 'same':
                $resultLine = "{$indent}  {$key}: {$notParentValue}";
                break;
            case 'changed':
                $old = 0;
                $new = 1;
                $oldValue = getValue($value[$old], $level);
                $newValue = getValue($value[$new], $level);
                $resultLine = "{$indent}- {$key}: {$oldValue}\n{$indent}+ {$key}: {$newValue}";
                break;
            case 'parent':
                $resArr = makeOutputArray($value, ++$level);
                $value = implode(PHP_EOL, $resArr);
                $minIndent = getIndent($level, "  ");
                $resultLine = "{$indent}  {$key}: {\n{$value}\n  {$indent}}";
                break;
        }

        return $resultLine;
    }, $diff);
}

function format(array $diff)
{
    $outputArr = makeOutputArray($diff);
    $output = implode(PHP_EOL, $outputArr);

    return "{\n{$output}\n}";
}
