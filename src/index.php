<?php

require_once "../vendor/autoload.php";

use Parser\SpringerParser;

$parser = new SpringerParser("http://link.springer.com/journal/volumesAndIssues/10791");
var_dump($parser->parse());