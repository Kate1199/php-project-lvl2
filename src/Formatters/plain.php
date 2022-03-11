<?php

namespace PHP\Project\Lvl2\Formatters\plain;

use function Functional\flatten;

function getValue(mixed $value)
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
        $type = $item['type'];

        $staticTemplate = "Property '{$property}' was";

        if ($type === 'added') {
            $addedValue = getValue($value);
            $messageIfAdded = [sprintf("%s added with value: %s", $staticTemplate, $addedValue)];
            return array_merge($acc, $messageIfAdded);
        } elseif ($type === 'removed') {
            $messageIfRemoved = [sprintf("%s removed", $staticTemplate)];
            return array_merge($acc, $messageIfRemoved);
        } elseif ($type === 'changed') {
            $old = 0;
            $new = 1;
            $oldValue = getValue($value[$old]);
            $newValue = getValue($value[$new]);

            $messageIfChanged = [sprintf("%s updated. From %s to %s", $staticTemplate, $oldValue, $newValue)];
            return array_merge($acc, $messageIfChanged);
        } elseif ($type === 'parent') {
            $parentKey = "{$property}.";
            return array_merge($acc, makeOutputArray($value, $parentKey));
        } else {
                return $acc;
        }
    }, []);
}

function formatPlain(array $diff): string
{
    $outputArr = makeOutputArray($diff);
    flatten($outputArr);

    return implode("\n", $outputArr);
}
