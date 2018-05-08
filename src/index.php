<?php

namespace Differ;

use Funct\Collection;

function genDiff($format, $pathToFileBefore, $pathToFileAfter)
{
    $fileDataBefore = file_get_contents($pathToFileBefore);
    $fileDataAfter = file_get_contents($pathToFileAfter);
    $objBefore = json_decode($fileDataBefore, true);
    $objAfter = json_decode($fileDataAfter, true);

    $ast = getAst($objBefore, $objAfter);
    return renderAst($ast);
}

function getAst($objBefore, $objAfter)
{
    $strPlus = '  + ';
    $strMinus = '  - ';
    $strTab = '    ';

    $united = Collection\union(array_keys($objAfter), array_keys($objBefore));
    $result = array_reduce($united, function ($acc, $key) use ($objBefore, $objAfter, $strPlus, $strMinus, $strTab) {
        if (array_key_exists($key, $objBefore) && array_key_exists($key, $objAfter)) {
            if ($objAfter[$key] === $objBefore[$key]) {
                $keyTab = $strTab . $key;
                $acc[$keyTab] = $objAfter[$key];
            } else {
                $keyPlus = $strPlus . $key;
                $keyMinus = $strMinus . $key;
                $acc[$keyPlus] = $objAfter[$key];
                $acc[$keyMinus] = $objBefore[$key];
            }
        } elseif (array_key_exists($key, $objAfter)) {
            $keyPlus = $strPlus . $key;
            $acc[$keyPlus] = $objAfter[$key];
        } else {
            $keyMinus = $strMinus . $key;
            $acc[$keyMinus] = $objBefore[$key];
        }
        return $acc;
    }, []);
    return $result;
}

function renderAst($ast)
{
    $outStr = array_reduce(array_keys($ast), function ($acc, $key) use ($ast) {
        $value = (is_bool($ast[$key])) ? var_export($ast[$key], true) : $ast[$key];
        return $acc . $key . ': ' . $value . PHP_EOL;
    }, '');
    return '{' . PHP_EOL . $outStr . '}';
}
