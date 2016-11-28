<?php
/**
 * Created by PhpStorm.
 * User: mova_sa
 * Date: 01.11.2016
 * Time: 9:41
 */

namespace Models;
use MySQL\DBQuery;
use naumenko_da\DBConnectionQuery\DBConnection;


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
        'year' => ' ',
        'journal' => ' ',
        'volume' => ' ',
        'issue' => ' ',
        'pages' => ' ',
        'doi' => ' ',
        'doiLink' => ' ',
        'paperTitle' => ' ',
        'completeCitation' => ' ',
        'abstract' => ' ',
        'views' => ' ',
        'citationGoogle' => ' ',
    ];

    function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return "";
    }

    function __set($name, $value)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name] = $value;
        }
    }


    public function toCSV($handler)
    {
        fputcsv($handler, $this->attributes);
        fflush($handler);
    }

    public function toString()
    {
        return implode(',', $this->attributes);
    }

    public function saveToDB()
    {
        $configs = include(__DIR__.'/../Config/defaults.conf.php');
        $dsn = 'mysql:dbname=' . $configs['db'].';host='.$configs['host'];
        $db = DBConnection::connect($dsn,$configs['user'],$configs['password']);
        $query = new DBQuery($db);
        $query->execute("set names utf8");

        $keys = [];
        $values = [];
        foreach ($this->attributes as $key =>  $value) {
            $keys[] = '`'. $key.'`';
            if(is_null($value)) {
                $values[] = '"\N"';
            } else {
                $values[] = '"'.$value.'"';
            }
        }

        $sql = 'INSERT INTO `springer`.`articles` ('.implode(',',$keys).' ) VALUES ('.implode(',',$values).')';
        $sql = str_replace('"\N"', 'null', $sql);

        $query->execute($sql);
    }
}