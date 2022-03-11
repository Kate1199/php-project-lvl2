<?php

namespace PHP\Project\Lvl2;

use PHPUnit\Framework\TestCase;

use function PHP\Project\Lvl2\Parsers\defineFileType;
use function PHP\Project\Lvl2\Parsers\makeAssociativeArray;

class ParsersTest extends TestCase
{
    private $parsedFile;

    public function setUp(): void
    {
        $this->parsedFile = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false,
            "keyParent" => [
                "key" => [
                    "two" => 2
                ]
            ]
        ];
    }

    public function testDefineFileType(): void
    {
        $expected1 = 'json';
        $actual1 = defineFileType('file.json');

        $this->assertEquals($expected1, $actual1);

        $expected2 = 'yml';
        $actual2 = defineFileType('file.yml');

        $this->assertEquals($expected2, $actual2);
    }

    public function testDefineFileTypeWithoutExtension(): void
    {
        $expected = '';
        $actual = defineFileType('file');

        $this->assertEquals($expected, $actual);
    }

    public function testMakeAssociativeArray()
    {
        $expected = $this->parsedFile;

        $actual1 = makeAssociativeArray('tests/fixtures/simpleFile1.json');
        $this->assertEquals($expected, $actual1);

        $actual2 = makeAssociativeArray('tests/fixtures/simpleFile1.yml');
        $this->assertEquals($expected, $actual2);
    }

    public function testMakeAssociativeArrayInvalidPath(): void
    {
        $expected = [];
        $actual = makeAssociativeArray('test');

        $this->assertEquals($expected, $actual);
    }
}
