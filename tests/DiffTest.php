<?php

namespace Php\Progect\Lvl2\Tests;

use PHPUnit\Framework\TestCase;

use function Php\Progect\Lvl2\Differ\compare;
use function Php\Progect\Lvl2\Differ\diffToString;

class DiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $file1 = json_decode(file_get_contents(__DIR__ . '/__fixtures__/file1.json'));
        $file2 = json_decode(file_get_contents(__DIR__ . '/__fixtures__/file2.json'));
        $expected = ["follow" => ['oldValue' => false, 'newValue' => '', 'status' => 'deleted'],
                     "host" => ['oldValue' => 'hexlet.io', 'newValue' => 'hexlet.io', 'status' => 'no_changed'],
                     'proxy' => ['oldValue' => '123.234.53.22', 'newValue' => '', 'status' => 'deleted'],
                     'timeout' => ['oldValue' => 50, 'newValue' => 20, 'status' => 'updated'],
                     'verbose' =>  ['oldValue' => '', 'newValue' => true, 'status' => 'added']];
        $expectedStr = "{
 - follow: false
   host: hexlet.io
 - proxy: 123.234.53.22
 - timeout: 50
 + timeout: 20
 + verbose: true
}";
        $actual = compare($file1, $file2);
        $this->assertEquals($actual, $expected);
        $this->assertEquals(diffToString($actual), $expectedStr);
    }
}
