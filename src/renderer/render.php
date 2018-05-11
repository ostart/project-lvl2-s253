<?php

namespace Differ\Render;

function rendAst($ast, $format)
{
    $renderList = [ 'pretty' => Pretty\rendAst($ast),
                    'plain'  => Plain\rendAst($ast),
                    'json'   => Json\rendAst($ast)];
    return $renderList[$format];
}
