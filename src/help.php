<?php

namespace PHP\Project\Lvl2\help;

use Docopt;

function getParams()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)
      gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                     Show this screen
      -v --version                  Show version
      --format <fmt>                Report format [default: stylish]
    DOC;

    return Docopt::handle($doc, ['version' => "1.0"]);
}
