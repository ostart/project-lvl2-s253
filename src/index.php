<?php

namespace Differ;

use Funct\Collection;
use function Parser\parse;
use function Render\rendAst;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $fileDataBefore = file_get_contents($pathToFileBefore);
    $fileDataAfter = file_get_contents($pathToFileAfter);
    $extBefore = (new \SplFileInfo($pathToFileBefore))->getExtension();
    $extAfter = (new \SplFileInfo($pathToFileAfter))->getExtension();
    if ($extBefore !== $extAfter) {
        throw new \Exception("File extensions can't be different.");
    }
    $arrBefore = parse($fileDataBefore, $extBefore);
    $arrAfter = parse($fileDataAfter, $extAfter);

    $ast = getAst($arrBefore, $arrAfter);
    return rendAst($ast, $format);
}

// function getAstOld($objBefore, $objAfter)
// {
//     $strPlus = '  + ';
//     $strMinus = '  - ';
//     $strTab = '    ';

//     $united = Collection\union(array_keys($objBefore), array_keys($objAfter));
//     $result = array_reduce($united, function ($acc, $key) use ($objBefore, $objAfter, $strPlus, $strMinus, $strTab) {
//         if (array_key_exists($key, $objBefore) && array_key_exists($key, $objAfter)) {
//             if ($objAfter[$key] === $objBefore[$key]) {
//                 $value = $strTab . $key . ': ' . checkForBool($objAfter[$key]);
//                 $acc[] = $value;
//             } else {
//                 $valPlus = $strPlus . $key . ': ' . checkForBool($objAfter[$key]);
//                 $valMinus = $strMinus . $key . ': ' . checkForBool($objBefore[$key]);
//                 $acc[] = $valPlus;
//                 $acc[] = $valMinus;
//             }
//         } elseif (array_key_exists($key, $objAfter)) {
//             $value = $strPlus . $key . ': ' . checkForBool($objAfter[$key]);
//             $acc[] = $value;
//         } else {
//             $value = $strMinus . $key . ': ' . checkForBool($objBefore[$key]);
//             $acc[] = $value;
//         }
//         return $acc;
//     }, []);
//     return $result;
// }

function getAst($arrBefore, $arrAfter)
{
    $united = Collection\union(array_keys($arrBefore), array_keys($arrAfter));
    $result = array_reduce($united, function ($acc, $key) use ($arrBefore, $arrAfter) {
        if (array_key_exists($key, $arrBefore) && array_key_exists($key, $arrAfter)) {
            if (is_array($arrBefore[$key]) && is_array($arrAfter[$key])) {
                $acc[] = ['type' => 'nested', 'key' => $key, 'children' => getAst($arrBefore[$key], $arrAfter[$key])];
            } elseif ($arrAfter[$key] === $arrBefore[$key]) {
                $acc[] = ['type' => 'fixed', 'key' => $key, 'value' => $arrAfter[$key]];
            } else {
                $acc[] = ['type' => 'updated', 'key' => $key,
                            'valBefore' => $arrBefore[$key], 'valAfter' => $arrAfter[$key]];
            }
        } elseif (array_key_exists($key, $arrAfter)) {
            $acc[] = ['type' => 'added', 'key' => $key, 'valAfter' => $arrAfter[$key]];
        } else {
            $acc[] = ['type' => 'deleted', 'key' => $key, 'valBefore' => $arrBefore[$key]];
        }
        return $acc;
    }, []);
    return $result;
}
