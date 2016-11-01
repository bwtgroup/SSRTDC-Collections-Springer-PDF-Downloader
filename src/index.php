<?php

require_once "../vendor/autoload.php";

use Parser\SpringerParser;

$data = getopt("l:f:");

if(!isset($data['l'])){
    echo "Cant start, no link to parse, use -l parameter to set link, example: php index.php -l=http://link.springer.com/journal/volumesAndIssues/10791".PHP_EOL;
    exit();
}

if(!isset($data['f'])){
    echo "Cant start, no file to save, use -f parameter to set file to save, example: php index.php -f=result.csv".PHP_EOL;
    exit();
}

$parser = new SpringerParser($data['l']);
$parser->parse($data['f']);
