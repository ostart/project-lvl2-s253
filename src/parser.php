<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse($fileContent, $fileExtension)
{
    switch ($fileExtension) {
        case 'json':
            return json_decode($fileContent, true);
        case 'yml':
        case 'yaml':
            return Yaml::parse($fileContent);
    }
}
