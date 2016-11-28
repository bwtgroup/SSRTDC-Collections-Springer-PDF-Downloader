<?php

require_once "../vendor/autoload.php";
set_time_limit(0);
error_reporting(1);
ini_set('display_errors', 1);

use Parser\SpringerParser;

$parser = new SpringerParser('');
$parser->parseGSViewsArtilesInDB();