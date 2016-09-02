<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 16/9/2
 * Time: 上午10:27
 */

$codeCity = require_once __DIR__."/CodeCity.php";

$codephp = '';
foreach ($codeCity as $code => $city) {
    $codephp .= <<<PHPCODE
  '$city' => $code,

PHPCODE;

}

$codephp = <<<PHPCODE
<?php

return array(
$codephp
);

PHPCODE;

file_put_contents('CityCode.php',$codephp);