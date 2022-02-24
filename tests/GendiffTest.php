<?php

namespace PHP\Project\Lvl2;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/autoload.php";
use function PHP\Project\Lvl2\gendiff\makeAssociativeArray;
use function PHP\Project\Lvl2\gendiff\convertBoolToStr;
use function PHP\Project\Lvl2\gendiff\getDiffArray;
use function PHP\Project\Lvl2\gendiff\gendiff;

class GendiffTest extends TestCase
{
    private $file1;
    private $file2;

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

    public function testGendiff()
    {
        $expected = <<<DIF
        - follow: false
          host: hexlet.io
        - proxy: 123.234.53.22
        - timeout: 50
        + timeout: 20
        + verbose: true
        
        DIF;
        $actual = gendiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');

        $this->assertEquals($expected, $actual);
    }
}
