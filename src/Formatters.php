<?php

namespace PHP\Project\Lvl2\Formatters;

use function PHP\Project\Lvl2\Formatters\stylish\formatStylish;
use function PHP\Project\Lvl2\Formatters\plain\formatPlain;

function formatChooser(string $format, array $diff): string
{
    switch ($format) {
        case 'stylish':
            $formattedDiff = formatStylish($diff);
            break;
        case 'plain':
            $formattedDiff = formatPlain($diff);
            break;
        default:
            $formattedDiff = '';
            break;
    }

    return $formattedDiff;
}
