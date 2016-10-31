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
    public $defaultHeaders = [
        "Cache-Control" => "max-age=0",
        "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
        "Accept-Encoding" => "gzip, deflate, lzma, sdch",
        "Accept-Language" => "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36 OPR/41.0.2353.46"
    ];

    public $url = "http://link.springer.com";
    public $journalLink = "journal/volumesAndIssues/";
    public $id = "";

    protected $journalLinks = [];

    public function __construct($link, array $headers = [], $proxy = "", $debug = true, $logFileName = "log.txt")
    {
        parent::__construct($headers, $proxy, $debug, $logFileName);
        $this->id = ltrim(strrchr($link, "/"), "/");
    }

    protected function parseJournalLinks()
    {
        $result = $this->sendRequest($this->journalLink . $this->id)->extractBody();
        $this->crawler->clear();
        $this->crawler->load($result);

        $this->journalLinks = [];

        foreach ($this->crawler->find('.issue-item') as $item){
           $this->journalLinks[] = $item->find('a',0)->href;
        }
    }

    protected function parseArticlesList($link){
        $result = [];

        $response = $this->sendRequest($link)->extractBody();
        $this->crawler->clear();
        $this->crawler->load($response);

        foreach ($this->crawler->find('ol') as $item){
              foreach($item->find('.title') as $articleLink){
                 $result[] = $articleLink->find('a',0)->href . PHP_EOL;
              }
        }


        return $result;
    }

    public function parse()
    {
        $this->parseJournalLinks();

         foreach ($this->journalLinks as $link){
            $this->parseArticlesList($link);
             exit();
         }

        return count($this->journalLinks);
    }
}