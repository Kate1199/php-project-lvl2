<?php

namespace PHP\Project\Lvl2\Parsers;

use Symfony\Component\Yaml\Yaml;

function defineFileType(string $filename): string
{
    $info = new \SplFileInfo($filename);

    return $info->getExtension();
}

function getContent(string $filename): string
{
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
    } else {
        return '';
    }

    if ($content === false) {
        return '';
    } else {
        return $content;
    }
}

function makeAssociativeArray(string $content, string $extension): array
{
    switch ($extension) {
        case 'json':
            $assocArr = json_decode($content, true);
            break;
        case ('yml' || 'yaml'):
            $assocArr = Yaml::parse($content);
            break;
        default:
            $assocArr = [];
            break;
    }

    return $assocArr;
}

function parseFile(string $filename): array
{
    $content = getContent($filename);
    $extension = defineFileType($filename);

    return makeAssociativeArray($content, $extension);
}
