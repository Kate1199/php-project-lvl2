<?php

namespace PHP\Project\Lvl2\Formatters;

use function PHP\Project\Lvl2\Formatters\stylish\formatStylish;
use function PHP\Project\Lvl2\Formatters\plain\formatPlain;
use function PHP\Project\Lvl2\Formatters\json\formatJson;

function formatChooser(string $format, array $diff): string
{
    switch ($format) {
        case 'stylish':
            $formattedDiff = formatStylish($diff);
            break;
        case 'plain':
            $formattedDiff = formatPlain($diff);
            break;
        case 'json':
            $formattedDiff = formatJson($diff);
            break;
        default:
            $formattedDiff = '';
            break;
    }

    return $formattedDiff;
}
