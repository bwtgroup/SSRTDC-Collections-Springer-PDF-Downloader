<?php

namespace Core;

use GuzzleHttp\Client;
use Helpers\SimpleHtmlDom;
use Exception\BodyNotFoundException;
use MySQL\DB;
use MySQL\DBQuery;

abstract class WebClient
{

    protected $defaultHeaders = [];
    protected $proxy;
    protected $proxyType;
    protected $url = "";
    private $logFileName = "";
    protected $response;
    public $db;

    public function __construct($headers = [], $proxy = "", $debug = false, $logFileName = "log.txt")
    {

        $this->logFileName = $logFileName;
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

        $this->client = new Client([
            'cookies' => true,
            'verify' => false,
            'headers' => $this->defaultHeaders,
            'base_uri' => $this->url,
        ]);
        $this->debug = $debug;
        $this->crawler = new SimpleHtmlDom(null, true, true, 'UTF-8', true, '\r\n', ' ');

        $db = DB::connect('mysql:dbname=proxylist;host=192.168.10.207', 'root', 'zxcvbnm123456Zx');
        $db2 = DB::connect('mysql:dbname=gavnosayty;host=192.168.10.253', 'root', 'peLGni4Q9MGmp');
        $this->db = new DBQuery($db2);
        $this->changeProxy();
    }

    public function changeProxy()
    {
        if (!empty($this->proxy)) {
            $this->db->execute("UPDATE proxies SET lastcheck = now(), lastcode=500 where ip = '" . $this->proxy . "'");
        }
        echo '=change proxy  ' . time() . PHP_EOL;

        $row = $this->db->queryRow("SELECT * FROM proxies WHERE lastcode = 200 order by rand() limit 1");
        if (isset($row) && !empty($row['ip'])) {
            echo '      proxy  old=' . $this->proxy . '; new=' . $row['ip'] . ' ( ' . $row['id'] . ' )' . PHP_EOL;

            $this->proxy = $row['ip'];
            $this->proxyType = $row['type'];

            return true;
        } else {
//            sleep(1);

            return $this->changeProxy();
        }
    }

    public function changeProxy2()
    {
        if (!empty($this->proxy)) {
            $this->db->execute("UPDATE proxies SET lastcheck = now(), lastcode=500 where ip = '" . $this->proxy . "'");
        }

        while (true) {
            echo '=change proxy  ' . time() . PHP_EOL;

            $row = $this->db->queryRow("SELECT * FROM proxies WHERE lastcode = 200 order by rand() limit 1");
            if (isset($row) && !empty($row['ip'])) {
                echo '      proxy  old=' . $this->proxy . '; new=' . $row['ip'] . ' ( ' . $row['id'] . ' )' . PHP_EOL;

                $this->proxy = $row['ip'];
                $this->proxyType = $row['type'];

                return true;
            } else {
                if (!empty($this->proxy)) {
                    $this->db->execute("UPDATE proxies SET lastcheck = now(), lastcode=500 where ip = '" . $this->proxy . "'");
                }

                sleep(5);
            }
        }

        return false;
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
        try {
            $this->response = $this->client->request($method, $url, $options);
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
            exit();
            echo "Cant get page: " . $url . PHP_EOL;
        }

        return $this;
    }

    protected function extractBody()
    {
        $body = (string)$this->response->getBody();

        if (empty($body)) {
            throw new BodyNotFoundException();
        }

        return $body;
    }

    public function curlRequest($url, $proxy = "", $type = CURLPROXY_HTTP)
    {
        $rep = 0;
        $ss = '';
        do {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if (!empty($proxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                curl_setopt($ch, CURLOPT_PROXYTYPE, $type);
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_REFERER, "https://scholar.google.com.ua/scholar");
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36 OPR/41.0.2353.46');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
//                "Accept-Encoding: gzip, deflate, lzma, sdch",
            ]);
            $ss = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            $rep++;
        } while (($code != '200') && ($rep < 4));

        return [
            'data' => $ss,
            'code' => $code,
        ];
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