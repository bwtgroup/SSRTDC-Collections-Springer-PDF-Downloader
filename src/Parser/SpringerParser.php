<?php
/**
 * Created by PhpStorm.
 * User: mova_sa
 * Date: 31.10.2016
 * Time: 17:09
 */

namespace Parser;

use Core\WebClient;
use Models\Article;

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


    public function __construct($link, array $headers = [], $proxy = "", $debug = false, $logFileName = "log.txt")
    {
        parent::__construct($headers, $proxy, $debug, $logFileName);
        $this->id = ltrim(strrchr($link, "/"), "/");
    }

    protected function parseJournalLinks()
    {
        $result = $this->sendRequest($this->journalLink . $this->id)->extractBody();
        $this->crawler->clear();
        $this->crawler->load($result);

        $res = [];

        foreach ($this->crawler->find('.issue-item') as $item) {
            $res[] = $item->find('a', 0)->href;
        }

        return $res;
    }

    protected function parseArticlesList($link)
    {
        $result = [];

        $response = $this->sendRequest($link)->extractBody();
        $this->crawler->clear();
        $this->crawler->load($response);

        foreach ($this->crawler->find('ol') as $item) {
            foreach ($item->find('.title') as $articleLink) {
                $result[] = $articleLink->find('a', 0)->href;
            }
        }


        return $result;
    }

    private function str_replace_first($from, $to, $subject)
    {
        $from = '/' . preg_quote($from, '/') . '/';

        return preg_replace($from, $to, $subject, 1);
    }

    protected function parseArticle($articleLink)
    {
        $response = $this->sendRequest($articleLink)->extractBody();

        $this->crawler->clear();
        $this->crawler->load($response);

        $article = new Article();
        $article->year = filter_var($this->crawler->find('.ArticleCitation_Year', 0)->plaintext, FILTER_SANITIZE_NUMBER_INT);
        $article->journal = trim($this->crawler->find('.JournalTitle', 0)->plaintext);
        $article->volume = filter_var($this->crawler->find('.ArticleCitation_Volume', 0)->plaintext, FILTER_SANITIZE_NUMBER_INT);
        $article->issue = filter_var($this->crawler->find('.ArticleCitation_Issue', 0)->plaintext, FILTER_SANITIZE_NUMBER_INT);
        $article->pages = trim($this->str_replace_first("pp", "", $this->crawler->find('.ArticleCitation_Pages', 0)->plaintext));
        $article->doi = $this->str_replace_first("article/", "", strrchr($articleLink, "article/"));
        $article->doiLink = $this->url . $articleLink;
        $article->paperTitle = trim($this->crawler->find('.MainTitleSection', 0)->plaintext);

        $authors = [];
        foreach ($this->crawler->find('.authors__name') as $author) {
            $authors[] = trim($author->plaintext);
        }
        $article->completeCitation = implode(", ", $authors) . ": " . $article->paperTitle . ". " .
            $article->journal . "., " . $article->volume . "(" . $article->issue . ") " . $article->pages . " (" . $article->year . ") DOI: " . $article->doi;

        $article->abstract = $this->str_replace_first("Abstract", "", $this->crawler->find('.Abstract', 0)->plaintext);
        $article->views = $this->crawler->find('.article-metrics__views', 0)->plaintext;
        $article->citationGoogle = $this->getGSViews($article->paperTitle);

        return $article;
    }

    protected function getGSViews($name, $proxy = "")
    {
        $requestResult = $this->sendRequest("https://scholar.google.com.ua/scholar?q=" . urlencode($name), [
            'headers' => [
                'Pragma' => 'no-cache',
                'Referer' => "https://scholar.google.com.ua/scholar",
            ],
            'proxy' => $proxy
        ])->extractBody();

        $this->crawler->clear();
        $this->crawler->load($requestResult);

        return intval(str_replace('Цитируется: ', '', $this->crawler->find('div.gs_fl', 1)->plaintext));
    }

    public function parse($fileName)
    {
        if($fileName == ""){
            echo "Error CSV file not set";
            exit();
        }

        $file = fopen($fileName, "w");
        $links = $this->parseJournalLinks();
        foreach ($links as $link) {
            $articles = $this->parseArticlesList($link);

            foreach ($articles as $article) {
                $this->parseArticle($article)->toCSV($file);
            }
        }

        fclose($file);
    }
}