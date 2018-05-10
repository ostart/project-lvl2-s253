<?php

namespace Render;

use function Lib\checkForBool;

class PlainRender implements IRender
{
    public function rendAst($ast)
    {
        return $this->rendPlain($ast);
    }

    private function rendPlain($ast, $keyPathArr = [])
    {
        $outStrArr = array_reduce($ast, function ($acc, $node) use ($keyPathArr) {
            ['key' => $key] = $node;
            $keyPathArr[] = $key;
            $keyPath = implode('.', $keyPathArr);
            switch ($node['type']) {
                case 'added':
                    ['valAfter' => $valAfter] = $node;
                    $value = $this->stringify($valAfter);
                    $acc[] = "Property '{$keyPath}' was added with value: '{$value}'";
                    break;
                case 'deleted':
                    ['valBefore' => $valBefore] = $node;
                    $acc[] = "Property '{$keyPath}' was removed";
                    break;
                case 'updated':
                    ['valBefore' => $valBefore, 'valAfter' => $valAfter] = $node;
                    $before = $this->stringify($valBefore);
                    $after = $this->stringify($valAfter);
                    $acc[] = "Property '{$keyPath}' was changed. From '{$before}' to '{$after}'";
                    break;
                case 'nested':
                    ['children' => $children] = $node;
                    $acc[] = $this->rendPlain($children, $keyPathArr);
                    break;
                case 'fixed':
                    break;
            }
            return $acc;
        }, []);
        return implode(PHP_EOL, $outStrArr);
    }

    private function stringify($obj)
    {
        return is_array($obj) ? 'complex value' : checkForBool($obj);
    }
}
