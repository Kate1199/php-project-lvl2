<?php

namespace PHP\Project\Lvl2;

use PHPUnit\Framework\TestCase;

use function PHP\Project\Lvl2\gendiff\getDiffByKey;
use function PHP\Project\Lvl2\gendiff\getChildrenDiff;
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
            "follow" => false,
            "keyParent" => [
                "two" => 2
            ]
        ];

        $this->file2 = [
            "timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io",
            "keyParent" => [
                "two" => -2
            ]
        ];

        $this->diff = [
            ['type' => 'removed', 'key' => 'follow', 'value' => false],
            ['type' => 'same', 'key' => 'host', 'value' => 'hexlet.io'],
            ['type' => 'parent', 'key' => 'keyParent',
                'value' => [
                    ['type' => 'changed', 'key' => 'two', 'value' => [2, -2]]
                ]
            ],
            ['type' => 'removed', 'key' => 'proxy', 'value' => '123.234.53.22'],
            ['type' => 'changed', 'key' => 'timeout', 'value' => [50, 20]],
            ['type' => 'added', 'key' => 'verbose', 'value' => true]
        ];
    }

    public function testGetDiffByKey()
    {
        $expected1 = ['type' => 'added', 'key' => 'verbose', 'value' => true];
        $actual1 = getDiffByKey($this->file1, $this->file2, 'verbose');

        $this->assertEquals($expected1, $actual1);

        $expected2 = ['type' => 'removed', 'key' => 'proxy', 'value' => '123.234.53.22'];
        $actual2 = getDiffByKey($this->file1, $this->file2, 'proxy');

        $this->assertEquals($expected2, $actual2);

        $expected3 = ['type' => 'same', 'key' => 'host', 'value' => 'hexlet.io'];
        $actual3 = getDiffByKey($this->file1, $this->file2, 'host');

        $this->assertEquals($expected3, $actual3);

        $expected4 = ['type' => 'changed', 'key' => 'timeout', 'value' => [50, 20]];
        $actual4 = getDiffByKey($this->file1, $this->file2, 'timeout');

        $this->assertEquals($expected4, $actual4);

        $expected5 = ['type' => 'parent', 'key' => 'keyParent',
            'value' => [
                ['type' => 'changed', 'key' => 'two', 'value' => [2, -2]]
            ]
        ];
        $actual5 = getDiffByKey($this->file1, $this->file2, 'keyParent');

        $this->assertEquals($expected5, $actual5);
    }

    public function testGetDiffByKeyInvalidKey(): void
    {
        $expected = [];
        $actual = getDiffByKey($this->file1, $this->file2, 'invalid');

        $this->assertEquals($expected, $actual);
    }

    public function testGetChildrenDiff(): void
    {
        $expected = $this->diff;
        $actual = getChildrenDiff($this->file1, $this->file2);

        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffJson()
    {
        $expected = $this->diff;
        $actual = genDiff('tests/fixtures/simpleFile1.json', 'tests/fixtures/simpleFile2.json');

        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffInvalidFilename()
    {
        $expected = [];
        $actual = genDiff('test1', 'test2');

        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffYaml()
    {
        $expected = $this->diff;
        $actual = genDiff('tests/fixtures/simpleFile1.yml', 'tests/fixtures/simpleFile2.yml');

        $this->assertEquals($expected, $actual);
    }
}
