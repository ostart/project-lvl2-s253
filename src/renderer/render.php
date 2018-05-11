<?php

namespace Render;

function rendAst($ast, $format)
{
    switch ($format) {
        case 'pretty':
            return rendPretty($ast);
        case 'plain':
            return rendPlain($ast);
        case 'json':
            return rendJson($ast);
    }
}
