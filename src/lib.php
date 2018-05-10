<?php

namespace Lib;

function checkForBool($value)
{
    return is_bool($value) ? var_export($value, true) : $value;
}
