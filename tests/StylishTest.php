<?php

namespace PHP\Project\Lvl2;

use PHPUnit\Framework\TestCase;

use function PHP\Project\Lvl2\stylish\arrayToStr;
use function PHP\Project\Lvl2\stylish\makeOutputArray;
use function PHP\Project\Lvl2\stylish\format;

class StylishTest extends TestCase
{
    private $arr;
    private $diff;
    private $formattedStr;

    public function setUp(): void
    {
        $this->arr = [
          "host" => "hexlet.io",
          "timeout" => 50,
          "proxy" => "123.234.53.22",
          "follow" => false,
          "keyParent" => [
              "two" => 2
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

    public function testArrayToStr(): void
    {
        $arr = ['key1' => 'str1', 'key2' => 'str2'];
        $expected1 = <<<STR
          key1: str1
          key2: str2
        STR;
        $actual1 = arrayToStr($arr);

        $this->assertEquals($expected1, $actual1);

        $expected2 = <<<STR
              key1: str1
              key2: str2
        STR;
        $actual2 = arrayToStr($arr, 1);

        $this->assertEquals($expected2, $actual2);

        $arr2 = [
            'key1' => [
                'key2' => 'value'
            ]
        ];

        $expected3 = <<<STR
          key1: {
              key2: value
          }
        STR;
        $actual3 = arrayToStr($arr2);

        $this->assertEquals($expected3, $actual3);
    }

    public function testMakeOutputArray(): void
    {
        $diff = [
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

        $expected = [
          "- follow: false",
          "  host: hexlet.io",
          <<<STR
            keyParent: {
              - two: 2
              + two: -2
            }
          STR,
          "- proxy: 123.234.53.22",
          "- timeout: 50\n+ timeout: 20",
          "+ verbose: true"
        ];
        $actual = makeOutputArray($this->diff);

        $this->assertEquals($expected, $actual);
    }

    public function testFormat(): void
    {
        $expected = <<<STR
        {
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

        $actual = format($this->diff);

        $this->assertEquals($expected, $actual);
    }
}
