<?php
/**
 * Created by PhpStorm.
 * User: 79636
 * Date: 12.07.2020
 * Time: 2:59
 */

namespace FinalQuest;


class Query
{

    public static function get($url) {
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
        curl_setopt($curl, CURLOPT_ENCODING ,"");
        $result=curl_exec($curl);
        if($result===false)
            return [];
        else
            return $result;


    }

}