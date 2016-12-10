<?php

use GuzzleHttp\Exception\RequestException;

require_once "../vendor/autoload.php";

if ($argv[1] && $argv[2]) {
    $generator = new ArticleGenerator($argv[1], $argv[2]);
    $generator->run();
} else {
    echo 'Empty arguments. Enter csv file path and output folder path' . PHP_EOL;
}

class ArticleGenerator
{

    private $list;
    private $outFolder;
    private $parsedList;
    private $client;
    private $prefix = 'http://link.springer.com/';

    /**
     * ArticleGenerator constructor.
     */
    public function __construct($list, $outFolder)
    {
        $this->list = $list;
        $this->outFolder = $outFolder;
        $this->client = new GuzzleHttp\Client();
    }

    public function run()
    {
        if ( ! $this->checkListFileExists()) {
            echo 'File does not exists' . PHP_EOL;

            return;
        } elseif ( ! $this->checkOutputFolderExists()) {
            echo 'Folder does not exists' . PHP_EOL;

            return;
        }
        $this->parseList();
        foreach ($this->parsedList as $item) {
            $file = $this->getFile($item['DOI']);
            if ( ! $file) {
                continue;
            }
            $filename = $this->outFolder . '/' . $item['ID'] . '-' . $item['year'] . '-' . $item['tome'] . '(' . $item['release'] . ')-(' . $item['pages'] . ')-' . preg_replace('/(.*\/)?(.+)/',
                    '$2', $item['DOI']) . $file['ext'];
            $handle = fopen($filename, 'w');
            fwrite($handle, $file['body']->getContents());
            fclose($handle);
            $this->sleepTenSeconds();
        }
        echo 'Jobs done' . PHP_EOL;
    }

    private function checkOutputFolderExists()
    {
        return file_exists($this->outFolder);
    }

    private function checkListFileExists()
    {
        if (file_exists($this->list . '.csv')) {
            $this->list .= '.csv';
        }

        return file_exists($this->list);
    }

    private function parseList()
    {
        $handle = fopen($this->list, 'r');
        while (($data = fgetcsv($handle, 0, ",")) !== false) {
            if ($data[0] && $data[1] && $data[2] && $data[3] && $data[4] && $data[5]) {
                $this->parsedList[] = [
                    'year' => $data[0],
                    'ID' => $data[1],
                    'tome' => $data[2],
                    'release' => $data[3],
                    'pages' => $data[4],
                    'DOI' => ltrim($data[5])
                ];
            } else {
                continue;
            }
        }
    }

    private function getFile($DOI)
    {
        try {
            $page = $this->client->request('GET', $this->prefix . $DOI . '.pdf');
            if ($page->getStatusCode() == 200 && in_array('application/pdf', $page->getHeader('content-type'))) {
                return ['body' => $page->getBody(), 'ext' => '.pdf'];
            } else {
                throw new Exception('No access');
            }
        } catch (Exception $e) {
            try {
                $page = $this->client->request('GET', $this->prefix . $DOI . '.html');
                if ($page->getStatusCode() == 200 && strpos($page->getBody()->getContents(),
                        'Log in to check your access to this article') === false
                ) {
                    return ['body' => $page->getBody(), 'ext' => '.html'];
                }

            } catch (Exception $exception) {
            }
        }

        echo 'File "' . $this->prefix . $DOI . '" was not found or it can\'t be reached' . PHP_EOL;
        $this->sleepTenSeconds();

        return false;
    }

    private function sleepTenSeconds()
    {
        sleep(10);
    }
}