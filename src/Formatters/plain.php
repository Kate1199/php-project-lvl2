<?php

namespace PHP\Project\Lvl2\Formatters\plain;

use function Functional\flatten;

function getValue($value)
{
    $boolToStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;
    $arrToStr = is_array($value) ? "[complex value]" : $boolToStr;
    $nullToStr = is_null($value) ? 'null' : $arrToStr;
    $str = is_string($value) ? "'{$value}'" : $nullToStr;

    return $str;
}

function makeOutputArray(array $diff, string $parentKeys = ""): array
{
    return array_reduce($diff, function ($acc, $item) use ($parentKeys) {
        $key = $item['key'];
        $property = "{$parentKeys}{$key}";
        $value = $item['value'];

        $staticTemplate = "Property '{$property}' was";

        switch ($item['type']) {
            case 'added':
                $addedValue = getValue($value);
                $acc[] = sprintf("%s added with value: %s", $staticTemplate, $addedValue);
                break;
            case 'removed':
                $acc[] = "{$staticTemplate} removed";
                break;
            case 'changed':
                $old = 0;
                $new = 1;
                $oldValue = getValue($value[$old]);
                $newValue = getValue($value[$new]);
                $acc[] = sprintf("%s updated. From %s to %s", $staticTemplate, $oldValue, $newValue);
                break;
            case 'parent':
                $parentKey = "{$property}.";
                $acc[] = makeOutputArray($value, $parentKey);
                break;
            default:
                break;
        }

        return flatten($acc);
    }, []);
}

function formatPlain(array $diff): string
{
    $outputArr = makeOutputArray($diff);

    return implode("\n", $outputArr);
}
