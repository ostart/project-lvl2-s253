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

    public function testJsonRecursGenDiff()
    {
        $expected = file_get_contents('tests/fixtures/expectedRecurs.txt');
        $this->assertEquals($expected, genDiff('tests/fixtures/beforeRecurs.json', 'tests/fixtures/afterRecurs.json'));
    }

    public function testYamlRecursGenDiff()
    {
        $expected = file_get_contents('tests/fixtures/expectedRecurs.txt');
        $this->assertEquals($expected, genDiff('tests/fixtures/beforeRecurs.yml', 'tests/fixtures/afterRecurs.yml'));
    }

    public function testPlainOutputGenDiff()
    {
        $expected = file_get_contents('tests/fixtures/expectedRecursPlain.txt');
        $this->assertEquals($expected, genDiff(
            'tests/fixtures/beforeRecurs.json',
            'tests/fixtures/afterRecurs.json',
            'plain'
        ));
    }
    public function testJsonOutputGenDiff()
    {
        $expected = file_get_contents('tests/fixtures/expectedRecursJson.txt');
        $this->assertEquals($expected, genDiff(
            'tests/fixtures/beforeRecurs.json',
            'tests/fixtures/afterRecurs.json',
            'json'
        ));
    }
}
