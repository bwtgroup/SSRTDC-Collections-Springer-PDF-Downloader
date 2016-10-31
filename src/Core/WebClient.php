<?php

namespace Core;

use GuzzleHttp\Client;
use Helpers\SimpleHtmlDom;

abstract class WebClient
{

    protected $defaultHeaders = [];
    protected $proxy;
    protected $url            = "";
    private   $logFileName    = "";

    protected $response;

    public function __construct($headers = [], $proxy = "", $debug = false, $logFileName = "log.txt")
    {
        $this->logFileName    = $logFileName;
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);
        $this->proxy          = $debug && empty($proxy) ? "127.0.0.1:8888" : $proxy;
        $this->client         = new Client([
            'cookies'  => true,
            'verify'   => false,
            'proxy'    => $this->proxy,
            'headers'  => $this->defaultHeaders,
            'base_uri' => $this->url
        ]);
        $this->debug          = $debug;
        $this->crawler        = new SimpleHtmlDom(null, true, true, 'UTF-8', true, '\r\n', ' ');
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param string $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return string
     */
    public function getLogFileName()
    {
        return $this->logFileName;
    }

    /**
     * @param string $logFileName
     */
    public function setLogFileName($logFileName)
    {
        $this->logFileName = $logFileName;
    }

    protected function sendRequest($url, $options = [], $method = "GET")
    {
        $this->response = $this->client->request($method, $url, $options);
        return $this;
    }

    protected function extractBody(){
        return (string)$this->response->getBody();
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    protected function log($string)
    {
        file_put_contents('log.txt', $string, FILE_APPEND);
    }

}