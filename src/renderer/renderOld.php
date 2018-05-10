<?php

namespace Render;

use Funct\Collection;

function rendAst($ast, $format)
{
    switch ($format) {
        case 'pretty':
            return rendPretty($ast);
        case 'plain':
            return rendPlain($ast);
    }
}

function rendPretty($ast, $lvl = 0)
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
                $embedded = rendPretty($children, $lvl + 1);
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

function rendPlain($ast, $keyPathArr = [])
{
    $outStrArr = array_reduce($ast, function ($acc, $node) use ($keyPathArr) {
        ['key' => $key] = $node;
        $keyPathArr[] = $key;
        $keyPath = implode('.', $keyPathArr);
        switch ($node['type']) {
            case 'added':
                ['valAfter' => $valAfter] = $node;
                $value = stringifyPlain($valAfter);
                $acc[] = "Property '{$keyPath}' was added with value: '{$value}'";
                break;
            case 'deleted':
                ['valBefore' => $valBefore] = $node;
                $acc[] = "Property '{$keyPath}' was removed";
                break;
            case 'updated':
                ['valBefore' => $valBefore, 'valAfter' => $valAfter] = $node;
                $before = stringifyPlain($valBefore);
                $after = stringifyPlain($valAfter);
                $acc[] = "Property '{$keyPath}' was changed. From '{$before}' to '{$after}'";
                break;
            case 'nested':
                ['children' => $children] = $node;
                $acc[] = rendPlain($children, $keyPathArr);
                break;
            case 'fixed':
                break;
        }
        return $acc;
    }, []);
    return implode(PHP_EOL, $outStrArr);
}

function stringifyPlain($obj)
{
    return is_array($obj) ? 'complex value' : checkForBool($obj);
}

function checkForBool($value)
{
    return is_bool($value) ? var_export($value, true) : $value;
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
