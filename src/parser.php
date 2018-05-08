<?php

namespace Parser;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

function parse($fileContent, $fileExtension)
{
    switch ($fileExtension) {
        case 'json':
            return json_decode($fileContent, true);
        case 'yml':
        case 'yaml':
            //return (new Parser())->parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
            return Yaml::parse($fileContent);
    }
}
