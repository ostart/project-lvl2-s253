<?php

namespace Differ;

use Funct\Collection;
use function Parser\parse;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $fileDataBefore = file_get_contents($pathToFileBefore);
    $fileDataAfter = file_get_contents($pathToFileAfter);
    $extBefore = (new \SplFileInfo($pathToFileBefore))->getExtension();
    $extAfter = (new \SplFileInfo($pathToFileAfter))->getExtension();
    if ($extBefore !== $extAfter) {
        throw new \Exception("Files extension can't be different.");
    }
    $objBefore = parse($fileDataBefore, $extBefore);
    $objAfter = parse($fileDataAfter, $extAfter);

    $ast = getAst($objBefore, $objAfter);
    return renderAst($ast);
}

function getAst($objBefore, $objAfter)
{
    $strPlus = '  + ';
    $strMinus = '  - ';
    $strTab = '    ';

    $united = Collection\union(array_keys($objBefore), array_keys($objAfter));
    $result = array_reduce($united, function ($acc, $key) use ($objBefore, $objAfter, $strPlus, $strMinus, $strTab) {
        if (array_key_exists($key, $objBefore) && array_key_exists($key, $objAfter)) {
            if ($objAfter[$key] === $objBefore[$key]) {
                $value = $strTab . $key . ': ' . checkForBool($objAfter[$key]);
                $acc[] = $value;
            } else {
                $valPlus = $strPlus . $key . ': ' . checkForBool($objAfter[$key]);
                $valMinus = $strMinus . $key . ': ' . checkForBool($objBefore[$key]);
                $acc[] = $valPlus;
                $acc[] = $valMinus;
            }
        } elseif (array_key_exists($key, $objAfter)) {
            $value = $strPlus . $key . ': ' . checkForBool($objAfter[$key]);
            $acc[] = $value;
        } else {
            $value = $strMinus . $key . ': ' . checkForBool($objBefore[$key]);
            $acc[] = $value;
        }
        return $acc;
    }, []);
    return $result;
}

function renderAst($ast)
{
    $outStr = implode(PHP_EOL, $ast);
    return '{' . PHP_EOL . $outStr .  PHP_EOL . '}';
}

function checkForBool($value)
{
    return is_bool($value) ? var_export($value, true) : $value;
}
