<?php

namespace Differ\Render\Plain;

use function Differ\Lib\checkForBool;

function rendAst($ast, $keyPathArr = [])
{
    $outStrArr = array_reduce($ast, function ($acc, $node) use ($keyPathArr) {
        ['key' => $key] = $node;
        $keyPathArr[] = $key;
        $keyPath = implode('.', $keyPathArr);
        switch ($node['type']) {
            case 'added':
                ['valAfter' => $valAfter] = $node;
                $value = stringify($valAfter);
                $acc[] = "Property '{$keyPath}' was added with value: '{$value}'";
                break;
            case 'deleted':
                ['valBefore' => $valBefore] = $node;
                $acc[] = "Property '{$keyPath}' was removed";
                break;
            case 'updated':
                ['valBefore' => $valBefore, 'valAfter' => $valAfter] = $node;
                $before = stringify($valBefore);
                $after = stringify($valAfter);
                $acc[] = "Property '{$keyPath}' was changed. From '{$before}' to '{$after}'";
                break;
            case 'nested':
                ['children' => $children] = $node;
                $acc[] = rendAst($children, $keyPathArr);
                break;
            case 'fixed':
                break;
        }
        return $acc;
    }, []);
    return implode(PHP_EOL, $outStrArr);
}

function stringify($obj)
{
    return is_array($obj) ? 'complex value' : checkForBool($obj);
}
