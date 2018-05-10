<?php

namespace Render;

use Funct\Collection;
use function Lib\checkForBool;

class PrettyRender implements IRender
{
    public function rendAst($ast)
    {
        return $this->rendPretty($ast);
    }

    private function rendPretty($ast, $lvl = 0)
    {
        $result = array_map(function ($node) use ($lvl) {
            switch ($node['type']) {
                case 'updated':
                    ['key' => $key, 'valBefore' => $valBefore, 'valAfter' => $valAfter] = $node;
                    $valFrom = $this->getStrFromObj($valBefore, $lvl + 1);
                    $valTo = $this->getStrFromObj($valAfter, $lvl + 1);
                    return [str_repeat($this->getIndent('fixed'), $lvl) .
                                $this->getIndent('added') . $key . ': ' . $valTo,
                            str_repeat($this->getIndent('fixed'), $lvl) .
                                $this->getIndent('deleted') . $key . ': ' . $valFrom];
                case 'nested':
                    ['key' => $key, 'children' => $children] = $node;
                    $embedded = $this->rendPretty($children, $lvl + 1);
                    return str_repeat($this->getIndent('fixed'), $lvl) .
                            $this->getIndent('fixed') . $key . ': ' . $embedded;
                case 'fixed':
                    ['key' => $key, 'value' => $value] = $node;
                    $val = $this->getStrFromObj($value, $lvl + 1);
                    return str_repeat($this->getIndent('fixed'), $lvl) .
                            $this->getIndent($node['type']) . $key . ': ' . $val;
                case 'added':
                    ['key' => $key, 'valAfter' => $valAfter] = $node;
                    $valTo = $this->getStrFromObj($valAfter, $lvl + 1);
                    return str_repeat($this->getIndent('fixed'), $lvl) .
                            $this->getIndent($node['type']) . $key . ': ' . $valTo;
                case 'deleted':
                    ['key' => $key, 'valBefore' => $valBefore] = $node;
                    $valFrom = $this->getStrFromObj($valBefore, $lvl + 1);
                    return str_repeat($this->getIndent('fixed'), $lvl) .
                            $this->getIndent($node['type']) . $key . ': ' . $valFrom;
            }
        }, $ast);
        $outStr = implode(PHP_EOL, Collection\flattenAll($result)) . PHP_EOL;
        return '{' . PHP_EOL . $outStr . str_repeat($this->getIndent('fixed'), $lvl) . '}';
    }

    private function getIndent($type)
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

    private function stringify($arr, $lvl = 0)
    {
        $keys = array_keys($arr);
        $strArr = array_reduce($keys, function ($acc, $key) use ($arr, $lvl) {
            $acc[] = str_repeat($this->getIndent('fixed'), $lvl + 1) . $key . ': ' . checkForBool($arr[$key]);
            return $acc;
        }, []);
        $outStr = implode(PHP_EOL, $strArr) . PHP_EOL;
        return '{' . PHP_EOL . $outStr . str_repeat($this->getIndent('fixed'), $lvl) . '}';
    }

    private function getStrFromObj($obj, $lvl = 0)
    {
        return is_array($obj) ? $this->stringify($obj, $lvl) : checkForBool($obj);
    }
}
