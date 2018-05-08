<?php

namespace Tests;

use \PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class TestSolution extends TestCase
{
    const RESULT = <<<DOC
{
  + timeout: 20
  - timeout: 50
  + verbose: true
    host: hexlet.io
  - proxy: 123.234.53.22
}
DOC;

    public function testGenDiff()
    {
        $this->assertEquals(self::RESULT, genDiff('json', 'tests/fixtures/before.json', 'tests/fixtures/after.json'));
    }
}
