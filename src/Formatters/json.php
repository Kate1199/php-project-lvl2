<?php

namespace PHP\Project\Lvl2\Formatters\json;

function formatJson(array $diff): string
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
