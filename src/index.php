<?php

require_once "../vendor/autoload.php";
set_time_limit(0);
error_reporting(0);
ini_set('display_errors', 0);

use Parser\SpringerParser;

$parser = new SpringerParser($argv[1]);
$parser->parse($argv[2]);