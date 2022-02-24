<?php

namespace PHP\Project\Lvl2\gendiff;

use function Functional\flatten;

function makeAssociativeArray(string $filename): array
{
    $content = file_get_contents($filename);
    $jsonArr = json_decode($content, true);
    return $jsonArr;
}

function convertBoolToStr($value): string
{
    $strValue = $value;

    if (is_bool($value)) {
        $strValue = $value ? 'true' : 'false';
    }

    return $strValue;
}

function getDiffArray(array $file1, array $file2, array $diff): array
{
    $output = [];
    $output = array_map(function ($key, $value) use ($file1, $file2) {

        $value = convertBoolToStr($value);

        if (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
            $file2[$key] = convertBoolToStr($file2[$key]);
            $file1[$key] = convertBoolToStr($file1[$key]);

            if ($file1[$key] !== $file2[$key]) {
                $result = ["- {$key}: {$file1[$key]}\n", "+ {$key}: {$file2[$key]}\n"];
            } else {
                $result = ["  {$key}: {$value}\n"];
            }
        } elseif (array_key_exists($key, $file1)) {
            $result = ["- {$key}: {$value}\n"];
        } else {
            $result = ["+ {$key}: {$value}\n"];
        }
        return $result;
    }, array_keys($diff), $diff);

    return flatten($output);
}

function genDiff(string $filename1, string $filename2): string
{
    $file1 = makeAssociativeArray($filename1);
    $file2 = makeAssociativeArray($filename2);

    $bothFiles = array_merge($file2, $file1);
    ksort($bothFiles);

    $output = getDiffArray($file1, $file2, $bothFiles);

    return implode($output);
}
