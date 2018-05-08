<?php

namespace Tests;

use \PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class TestSolution extends TestCase
{
    public function testJsonGenDiff()
    {
        $expected = file_get_contents('tests/fixtures/expected.txt');
        $this->assertEquals($expected, genDiff('tests/fixtures/before.json', 'tests/fixtures/after.json'));
    }

    public function testYamlGenDiff()
    {
        $expected = file_get_contents('tests/fixtures/expected.txt');
        $this->assertEquals($expected, genDiff('tests/fixtures/before.yml', 'tests/fixtures/after.yml'));
    }
}
