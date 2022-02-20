<?php

namespace PHP\Project\Lvl2\Help;

use Docopt;

function showHelp()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)

    Options:
      -h --help                     Show this screen
      -v --version                  Show version
    DOC;

    return Docopt::handle($doc);
}
