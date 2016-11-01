<?php
/**
 * Created by PhpStorm.
 * User: mova_sa
 * Date: 01.11.2016
 * Time: 9:41
 */

namespace Models;


/**
 * Class Article
 * @package Models
 * @property  string $year
 * @property  string $journal
 * @property  string $volume
 * @property  string $issue
 * @property  string $pages
 * @property  string $doi
 * @property  string $doiLink
 * @property  string $paperTitle
 * @property  string $completeCitation
 * @property  string $abstract
 * @property  string $views
 * @property  string $citationGoogle
 */
class Article
{
    protected $attributes = [
        'year' => '',
        'journal'=> '',
        'volume'=> '',
        'issue'=> '',
        'pages'=> '',
        'doi'=> '',
        'doiLink'=> '',
        'paperTitle'=> '',
        'completeCitation'=> '',
        'abstract'=> '',
        'views'=> '',
        'citationGoogle'=> '',
    ];

    function __get($name)
    {
        if(array_key_exists($name, $this->attributes)){
            return $this->attributes[$name];
        }

        return "";
    }

    function __set($name, $value)
    {
        if(array_key_exists($name, $this->attributes)){
            return $this->attributes[$name] = $value;
        }
    }


    public function toCSV($handler)
    {
        fputcsv($handler, $this->attributes);
    }

    public function toString()
    {
        return implode(PHP_EOL, $this->attributes);
    }
}