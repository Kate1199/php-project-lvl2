<?php

namespace PHP\Project\Lvl2;

use PHPUnit\Framework\TestCase;

use function PHP\Project\Lvl2\Parsers\makeAssociativeArray;
use function PHP\Project\Lvl2\gendiff\convertBoolToStr;
use function PHP\Project\Lvl2\gendiff\getDiffArray;
use function PHP\Project\Lvl2\gendiff\gendiff;

class GendiffTest extends TestCase
{
    private $file1;
    private $file2;
    private $diff;

    public function setUp(): void
    {
        $this->file1 = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false
        ];

        $this->file2 = [
            "timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io"
        ];

        $this->diff = <<<DIF
        - follow: false
          host: hexlet.io
        - proxy: 123.234.53.22
        - timeout: 50
        + timeout: 20
        + verbose: true
        
        DIF;
    }

    public function testMakeAssociativeArray()
    {
        $expected = $this->file1;
        $actual = makeAssociativeArray('tests/fixtures/file1.json');

        $this->assertEquals($expected, $actual);
    }

    public function testConvertBoolToStr()
    {
        $expected1 = 'false';
        $actual1 = convertBoolToStr(false);
        $this->assertEquals($expected1, $actual1);

        $expected2 = 'true';
        $actual2 = convertBoolToStr(true);
        $this->assertEquals($expected2, $actual2);
    }

    public function testConvertBoolToStrNotBool()
    {
        $this->assertEquals('a', convertBoolToStr('a'));
    }

    public function testGetDiffArray()
    {
        $expected = [
            "- follow: false\n",
            "  host: hexlet.io\n",
            "- proxy: 123.234.53.22\n",
            "- timeout: 50\n" ,
            "+ timeout: 20\n",
            "+ verbose: true\n"
        ];

        $both = array_merge($this->file2, $this->file1);
        ksort($both);
        $actual = getDiffArray($this->file1, $this->file2, $both);

        $this->assertEquals($expected, $actual);
    }

    public function testGetDiffArrayEmpty()
    {
        $expected = [
            "+ host: hexlet.io\n",
            "+ timeout: 20\n",
            "+ verbose: true\n"
        ];

        $both = $this->file2;
        ksort($both);
        $actual = getDiffArray([], $this->file2, $both);

        $this->assertEquals($expected, $actual);
    }

    public function testGendiffJson()
    {
        $expected = $this->diff;
        $actual = gendiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');

        $this->assertEquals($expected, $actual);
    }

    public function testGendiffInvalidFilename()
    {
        $expected = '';
        $actual = gendiff('test1', 'test2');

        $this->assertEquals($expected, $actual);
    }

    public function testGendiffYaml()
    {
        $expected = $this->diff;
        $actual = gendiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yml');

        $this->assertEquals($expected, $actual);
    }
}
