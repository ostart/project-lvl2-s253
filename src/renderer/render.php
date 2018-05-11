<?php

namespace Render;

function rendAst($ast, $format)
{
    $renderList = [ 'pretty' => rendPretty($ast),
                    'plain'  => rendPlain($ast),
                    'json'   => rendJson($ast)];
    return $renderList[$format];
}
