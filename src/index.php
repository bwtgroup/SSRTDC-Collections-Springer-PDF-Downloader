<?php

require_once "../vendor/autoload.php";
set_time_limit(0);
//error_reporting(0);
//ini_set('display_errors', 0);

use Parser\SpringerParser;

$data = $_GET;

if (!isset($data['link'])) {
    echo "Cant start, no link to parse, use \"link\" parameter to set link, example: http://springer.groupbwt.com/src/index.php?link=http://link.springer.com/journal/volumesAndIssues/10791&file=result.csv" . PHP_EOL;
    exit();
}

if (!isset($data['file'])) {
    echo "Cant start, no file to save, use \"file\" parameter to set file to save, example: http://springer.groupbwt.com/src/index.php?link=http://link.springer.com/journal/volumesAndIssues/10791&file=result.csv" . PHP_EOL;
    exit();
}

$parser = new SpringerParser($data['link']);
$parser->parse($data['file']);