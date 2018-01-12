#!/usr/bin/php
<?php

if (!isset($argv[1])) {
    $fname = explode("/", __FILE__);
    $fname = end($fname);

    echo "\n";
    echo "$fname: Missing domain" .PHP_EOL;
    echo "Usage: ./" . $fname . " domain" .PHP_EOL;
    echo "Example: ./" . $fname . " google.com" .PHP_EOL;
    echo "\n";
}else{
    $host = $argv[1];
    print_r(viewDns($host) . PHP_EOL);
}

function viewDns($url){
    $url = "http://viewdns.info/reverseip/?host=$url&t=1";
    $ch = curl_init();
 
    $headers   = array();
    $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
    $headers[] = "Accept-Encoding: gzip, deflate";
    $headers[] = "Accept-Language: en-US,en;q=0.8";
    $headers[] = "Connection: keep-alive";
    $headers[] = "Host: viewdns.info";
    $headers[] = "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.82 Safari/537.36";
    $headers[] = "Upgrade-Insecure-Requests: 1";
    $headers[] = "Referer: $url";
    $headers[] = "DNT: 1";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
 
    if (curl_errno($ch)) {
        return false;
    } else {

        $ch = curl_exec($ch);
        $ch = get_all_string_between($ch, "<!--<div>-->\n<table width=\"1000\" bgcolor=\"#FFFFFF\" style=\"border: 1px solid #CCCCCC; padding: 5px\" align=\"center\" id=\"null\">","</table>\n<!--</div>-->");
        $hd = get_all_string_between($ch[0], "<font size=\"2\" face=\"Courier\">", "<br>==============<br><br>");
        $tb = get_all_string_between($ch[0], "<table border=\"1\">", "</table>");
        $tbh = @str_replace("<tr><td>", "", str_replace("</td><td>", "\t", $tb[0]));
        $tbi = @str_replace("</td><td align=\"center\">", "\t", @str_replace("</td></tr><tr> <td>", "\n", $tbh));
        $ret = array(0 => $hd[0], 1 => str_replace("</td></tr>", "\n\n", preg_replace('/^.+\n/', '', $tbi)));
        $xp1 = @explode("\n", $ret[1]);
        $flt = array_filter($xp1);

        $res = array_map(function($val) {
            $val = explode("\t", $val);
            $val = array(0=>$val[1], 1=>$val[0]);
            return implode(" ", $val);
        }, $flt);

        $fnl['header'] = $ret[0]; 
        $fnl['total']  = count($res);
        //array_unshift($res , 'Resolve Dt Domain');
        $fnl['result'] = $res; 


        return json_encode($fnl, JSON_PRETTY_PRINT);
    }
}
 
function get_all_string_between($string, $start, $end){
    $result = array();
    $string = " ".$string;
    $offset = 0;
    while(true)
    {
        $ini = strpos($string,$start,$offset);
        if ($ini == 0)
            break;
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        $result[] = substr($string,$ini,$len);
        $offset = $ini+$len;
    }
    return $result;
}


