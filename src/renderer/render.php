<?php

namespace Render;

function getRender($format)
{
    switch ($format) {
        case 'pretty':
            return new PrettyRender();
        case 'plain':
            return new PlainRender();
    }
}
