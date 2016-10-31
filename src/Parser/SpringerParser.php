<?php
/**
 * Created by PhpStorm.
 * User: mova_sa
 * Date: 31.10.2016
 * Time: 17:09
 */

namespace Parser;

use Core\WebClient;

class SpringerParser extends WebClient
{
    public function __construct(array $headers = [], $proxy="", $debug=true, $logFileName="log.txt")
    {
        parent::__construct($headers, $proxy, $debug, $logFileName);
    }
}