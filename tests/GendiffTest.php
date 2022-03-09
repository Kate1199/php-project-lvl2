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

    private $stylishDiff;
    private $plainDiff;

    private $json1;
    private $json2;

    private $yml1;
    private $yml2;

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

        $this->stylishDiff = <<<STR
        {
        + add: {
              child: 5
          }
        - follow: false
          host: hexlet.io
          keyParent: {
            - two: 2
            + two: -2
          }
        - proxy: 123.234.53.22
        - timeout: 50
        + timeout: 20
        + verbose: true
        }
        STR;

        $this->plainDiff = <<<STR
        Property 'add' was added with value: [complex value]
        Property 'follow' was removed
        Property 'keyParent.two' was updated. From 2 to -2
        Property 'proxy' was removed
        Property 'timeout' was updated. From 50 to 20
        Property 'verbose' was added with value: true
        STR;

        $this->json1 = 'tests/fixtures/simpleFile1.json';
        $this->json2 = 'tests/fixtures/simpleFile2.json';

        $this->yml1 = 'tests/fixtures/simpleFile1.yml';
        $this->yml2 = 'tests/fixtures/simpleFile2.yml';
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
        $expected1 = $this->stylishDiff;
        $actual1 = genDiff($this->json1, $this->json2, 'stylish');

        $this->assertEquals($expected1, $actual1);

        $expected2 = $this->plainDiff;
        $actual2 = genDiff($this->json1, $this->json2, 'plain');

        $this->assertEquals($expected2, $actual2);
    }

    public function testGenDiffInvalidFilename()
    {
        $expected = '';
        $actual = genDiff('test1', 'test2', 'stylish');

        $this->assertEquals($expected, $actual);
    }

    public function testGenDiffYaml()
    {
        $expected1 = $this->stylishDiff;
        $actual1 = genDiff($this->yml1, $this->yml2, 'stylish');

        $this->assertEquals($expected1, $actual1);

        $expected2 = $this->plainDiff;
        $actual2 = genDiff($this->yml1, $this->yml2, 'plain');

        $this->assertEquals($expected2, $actual2);
    }

    public function testGenDiffFormatJson(): void
    {
        $expected = <<<STR
        [
            {"type":"added","key":"add","value":{"child":5}}
            {"type":"removed","key":"follow","value":false}
            {"type":"same","key":"host","value":"hexlet.io"}
            {"type":"parent","key":"keyParent","value":[{"type":"changed","key":"two","value":[2,-2]}]}
            {"type":"removed","key":"proxy","value":"123.234.53.22"}
            {"type":"changed","key":"timeout","value":[50,20]}
            {"type":"added","key":"verbose","value":true}
        ]
        STR;

        $actual1 = genDiff($this->json1, $this->json2, 'json');
        $this->assertEquals($expected, $actual1);

        $actual2 = gendiff($this->yml1, $this->yml2, 'json');
        $this->assertEquals($expected, $actual2);
    }
}
