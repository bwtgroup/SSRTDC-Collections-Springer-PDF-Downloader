<?php

require_once "../vendor/autoload.php";
set_time_limit(0);
error_reporting(1);
ini_set('display_errors', 1);

use Parser\SpringerParser;

//$parser = new SpringerParser('');
//$parser->parseGSViewsArtilesInDB();

$loop = React\EventLoop\Factory::create();

for($i=0; $i< 50; $i++) {
    $process = new React\ChildProcess\Process("php single.php");
    $process->start($loop);
}

$loop->run();