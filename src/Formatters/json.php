<?php

namespace PHP\Project\Lvl2\Formatters\json;

function formatJson(array $diff): string
{
    $outputArr = array_map(fn($item) => json_encode($item), $diff);
    $output = implode("\n    ", $outputArr);

    return "[\n    {$output}\n]";
}
