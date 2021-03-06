<?php

namespace Differ\Render\Pretty;

use Funct\Collection;
use function Differ\Lib\checkForBool;

function rendAst($ast, $lvl = 0)
{
    $result = array_map(function ($node) use ($lvl) {
        switch ($node['type']) {
            case 'updated':
                ['key' => $key, 'valBefore' => $valBefore, 'valAfter' => $valAfter] = $node;
                $valFrom = getStrFromObj($valBefore, $lvl + 1);
                $valTo = getStrFromObj($valAfter, $lvl + 1);
                return [str_repeat(getIndent('fixed'), $lvl) . getIndent('added') . $key . ': ' . $valTo,
                        str_repeat(getIndent('fixed'), $lvl) . getIndent('deleted') . $key . ': ' . $valFrom];
            case 'nested':
                ['key' => $key, 'children' => $children] = $node;
                $embedded = rendAst($children, $lvl + 1);
                return str_repeat(getIndent('fixed'), $lvl) . getIndent('fixed') . $key . ': ' . $embedded;
            case 'fixed':
                ['key' => $key, 'value' => $value] = $node;
                $val = getStrFromObj($value, $lvl + 1);
                return str_repeat(getIndent('fixed'), $lvl) . getIndent($node['type']) . $key . ': ' . $val;
            case 'added':
                ['key' => $key, 'valAfter' => $valAfter] = $node;
                $valTo = getStrFromObj($valAfter, $lvl + 1);
                return str_repeat(getIndent('fixed'), $lvl) . getIndent($node['type']) . $key . ': ' . $valTo;
            case 'deleted':
                ['key' => $key, 'valBefore' => $valBefore] = $node;
                $valFrom = getStrFromObj($valBefore, $lvl + 1);
                return str_repeat(getIndent('fixed'), $lvl) . getIndent($node['type']) . $key . ': ' . $valFrom;
        }
    }, $ast);
    $outStr = implode(PHP_EOL, Collection\flattenAll($result)) . PHP_EOL;
    return '{' . PHP_EOL . $outStr . str_repeat(getIndent('fixed'), $lvl) . '}';
}

function getIndent($type)
{
    switch ($type) {
        case 'fixed':
            return '    ';
        case 'added':
            return '  + ';
        case 'deleted':
            return '  - ';
    }
}

function stringify($arr, $lvl = 0)
{
    $keys = array_keys($arr);
    $strArr = array_reduce($keys, function ($acc, $key) use ($arr, $lvl) {
        $acc[] = str_repeat(getIndent('fixed'), $lvl + 1) . $key . ': ' . checkForBool($arr[$key]);
        return $acc;
    }, []);
    $outStr = implode(PHP_EOL, $strArr) . PHP_EOL;
    return '{' . PHP_EOL . $outStr . str_repeat(getIndent('fixed'), $lvl) . '}';
}

function getStrFromObj($obj, $lvl = 0)
{
    return is_array($obj) ? stringify($obj, $lvl) : checkForBool($obj);
}
