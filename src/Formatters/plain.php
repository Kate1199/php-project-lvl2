<?php

namespace PHP\Project\Lvl2\Formatters\plain;

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
        $property = sprintf("%s%s", $parentKeys, $key);
        $value = $item['value'];
        $type = $item['type'];

        $staticTemplate = sprintf("Property '%s' was", $property);

        if ($type === 'added') {
            $addedValue = getValue($value);
            $messageIfAdded = [sprintf("%s added with value: %s", $staticTemplate, $addedValue)];
            return array_merge($acc, $messageIfAdded);
        } elseif ($type === 'removed') {
            $messageIfRemoved = [sprintf("%s removed", $staticTemplate)];
            return array_merge($acc, $messageIfRemoved);
        } elseif ($type === 'changed') {
            $oldValue = getValue($value[0]);
            $newValue = getValue($value[1]);

            $messageIfChanged = [sprintf("%s updated. From %s to %s", $staticTemplate, $oldValue, $newValue)];
            return array_merge($acc, $messageIfChanged);
        } elseif ($type === 'parent') {
            $parentKey = sprintf("%s.", $property);
            return array_merge($acc, makeOutputArray($value, $parentKey));
        } else {
                return $acc;
        }
    }, []);
}

function formatPlain(array $diff): string
{
    $outputArr = makeOutputArray($diff);
    $flattened = collect($outputArr)->flatten($outputArr)->toArray(); // @phpstan-ignore-line

    return implode("\n", $flattened);
}
