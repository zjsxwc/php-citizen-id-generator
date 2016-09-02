<?php

/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 16/9/2
 * Time: 上午9:52
 */
class CitizenIDGenerator
{

    protected $salt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];

    protected $checksum = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];

    protected function calculateLastCode($code)
    {
        if (strlen($code) != 17) {
            throw new \RuntimeException('code length not meet 17 when calculate LastCode');
        }
        $sum = 0;
        for ($i = 0; $i < 17; $i ++) {
            $sum += $code{$i} * $this->salt{$i};
        }
        $seek = $sum % 11;

        return (string) $this->checksum[$seek];
    }

    public function generate($cityCode, $birthDay, $isMale)
    {
        if (strlen($cityCode) != 6){
            throw new \RuntimeException("cityCode {$cityCode} length not meet 6 when exec code generator");
        }
        if (strlen($birthDay) != 8){
            throw new \RuntimeException("birthDay {$birthDay} length not meet 8 when exec code generator");
        }

        $randCode = 0;
        while (true) {
            $randCode = rand(0,999);
            if ($isMale && (($randCode % 2) == 1)) {
                break;
            }
            if ((!$isMale) && (($randCode % 2) == 0)) {
                break;
            }
        }
        $randCode = str_pad($randCode, 3, "0", STR_PAD_LEFT);

        $code = $cityCode.$birthDay.$randCode;

        return $code.$this->calculateLastCode($code);
    }

    static public function cliExec()
    {

        if (!function_exists('readline')) {

            function readline($prompt)
            {
                echo $prompt;
                $input = '';
                while (1) {
                    $key = fgetc(STDIN);
                    switch ($key) {
                        case "\n":
                            return $input;

                        default:
                            $input .= $key;
                    }
                }
            }
        }

        entercityname:
        $cityName = readline("请输入城市名字>");
        $cityName = trim($cityName);
        $cityCodes = require_once __DIR__."/CityCode.php";
        if (!array_key_exists($cityName,$cityCodes)) {
            echo "不存在城市 {$cityName} 的6位城市码，城市名称可以参见CityCode.php".PHP_EOL;
            goto entercityname;
        }
        $cityCode = $cityCodes[$cityName];


        enterbirthday:
        $birthDay = readline("请8位的出生年月日>");
        $birthDay = trim($birthDay);
        if ((strlen($birthDay)!=8)||(!is_numeric($birthDay))) {
            echo "出生年月日不符合要求 {$birthDay} ".PHP_EOL;
            goto enterbirthday;
        }

        $isMale = readline("是男的吗(yes)>");
        $isMale = trim($isMale);
        $isMale = ($isMale == 'yes')?true:false;

        $instance = new static();
        $citizenID = $instance->generate($cityCode,$birthDay,$isMale);
        echo "身份证号码是{$citizenID}".PHP_EOL;
    }

}

