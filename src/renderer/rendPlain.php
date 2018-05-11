<?php

namespace Render;

use function Lib\checkForBool;

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
