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

        $messages = [];
        switch ($item['type']) {
            case 'added':
                $addedValue = getValue($value);
                $messages[] = sprintf("%s added with value: %s", $staticTemplate, $addedValue);
                break;
            case 'removed':
                $messages[] = sprintf("%s removed", $staticTemplate);
                break;
            case 'changed':
                $old = 0;
                $new = 1;
                $oldValue = getValue($value[$old]);
                $newValue = getValue($value[$new]);
                $messages[] = sprintf("%s updated. From %s to %s", $staticTemplate, $oldValue, $newValue);
                break;
            case 'parent':
                $parentKey = "{$property}.";
                $messages[] = makeOutputArray($value, $parentKey);
                break;
            default:
                break;
        }
        $acc[] = $messages;

        return flatten($acc);
    }, []);
}

function formatPlain(array $diff): string
{
    $outputArr = makeOutputArray($diff);

    return implode("\n", $outputArr);
}
