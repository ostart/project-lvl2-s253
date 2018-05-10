<?php

namespace Render;

class JsonRender implements IRender
{
    public function rendAst($ast)
    {
        return json_encode($ast);
    }
}
