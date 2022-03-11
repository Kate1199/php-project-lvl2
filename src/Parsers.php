<?php

namespace PHP\Project\Lvl2\Parsers;

use Symfony\Component\Yaml\Yaml;

function defineFileType(string $filename): string
{
    $pointPos = strpos($filename, '.');

    if ($pointPos === false) {
        return '';
    }

    return substr($filename, $pointPos + 1);
}

function makeAssociativeArray(string $filename): array
{
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
    } else {
        return [];
    }

    if ($content === false) {
        return [];
    } else {
        $realContent = $content;
    }

    $extension = defineFileType($filename);

    if ($extension === 'json') {
        $assocArr = json_decode($realContent, true);
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        $assocArr = Yaml::parse($realContent);
    } else {
        $assocArr = [];
    }

    return $assocArr;
}
