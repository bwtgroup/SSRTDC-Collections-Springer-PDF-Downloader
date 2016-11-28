<?php

$str1 = 'Preface to the Special Issue on &#39;Reconsidering “Awareness” in CSCW&#39;';
$str2 = 'Preface to the Special Issue on ‘Reconsidering “Awareness” in CSCW’';

//$str1 = htmlspecialchars_decode('Preface to the Special Issue on &#39;Reconsidering “Awareness” in CSCW&#39;', ENT_QUOTES);
//$str2 = htmlspecialchars_decode($str2, ENT_QUOTES);
//$str1 = preg_replace("/&#?[a-z0-9]{2,8};/i","",$str1);
//$str2 = filter_var($str2, FILTER_SANITIZE_STRING);

$str1 = html_entity_decode(htmlentities($str1));
echo $str1.PHP_EOL;
$str2 = html_entity_decode(htmlentities($str1));
echo $str2.PHP_EOL;

var_dump($str1 == $str2);
//echo $str1.PHP_EOL.$str2.PHP_EOL;

//var_dump(htmlspecialchars_decode($str1, ENT_QUOTES) == htmlspecialchars_decode($str2, ENT_QUOTES));

//var_dump(preg_match('/'.preg_replace('/[\W]+/','/[\W]+/',$str1).'/',$str2));