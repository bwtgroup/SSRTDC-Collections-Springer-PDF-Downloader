<?php
/**
 * Created by PhpStorm.
 * User: mova_sa
 * Date: 16.11.2016
 * Time: 17:27
 */

require_once "../vendor/autoload.php";
set_time_limit(0);

$loop = React\EventLoop\Factory::create();

$proxy = [];

$needToParse = [
    "http://link.springer.com/journal/volumesAndIssues/10791"=>"IRJ.csv",
    "http://link.springer.com/journal/volumesAndIssues/10489"=>"AI.csv",
    "http://link.springer.com/journal/volumesAndIssues/10844"=>"JoIIS.csv",
    "http://link.springer.com/journal/volumesAndIssues/10115"=>"KaIS.csv",
];

foreach($needToParse as $key=>$value) {
    $process = new React\ChildProcess\Process("php index.php ".$key." ".$value);
    $process->start($loop);
}

$loop->run();