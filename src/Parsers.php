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

    $extension = defineFileType($filename);

    if ($extension === 'json') {
        $assocArr = json_decode($content, true);
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        $assocArr = Yaml::parse($content);
    } else {
        $assocArr = [];
    }

    return $assocArr;
}
