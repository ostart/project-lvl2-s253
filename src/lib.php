<?php

namespace Differ\Lib;

function checkForBool($value)
{
    return is_bool($value) ? var_export($value, true) : $value;
}
