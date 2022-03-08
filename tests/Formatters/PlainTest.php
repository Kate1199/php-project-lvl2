<?php

namespace PHP\Project\Lvl2\plain;

use PHPUnit\Framework\TestCase;

use function PHP\Project\Lvl2\Formatters\plain\formatPlain;

class PlainTest extends TestCase
{
    public function testformatPlain(): void
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

        $expected = <<<STR
        Property 'follow' was removed
        Property 'keyParent.two' was updated. From 2 to -2
        Property 'proxy' was removed
        Property 'timeout' was updated. From 50 to 20
        Property 'verbose' was added with value: true
        STR;

        $actual = formatPlain($diff);

        $this->assertEquals($expected, $actual);
    }

    public function testformatPlainEmpty(): void
    {
        $expected = '';
        $actual = formatPlain([]);

        $this->assertEquals($expected, $actual);
    }
}
