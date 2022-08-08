<?php
define('URL_TAG', ['tagName'=>'a','attributeName'=>'href']);
define('IMG_TAG', ['tagName'=>'img','attributeName'=>'src']);
define('SCRIPT_TAG', ['tagName'=>'script','attributeName'=>'src']);

$parseUrl = isset($_SERVER['argv'][1]) && !empty($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';

function valid_URL($url){
    return preg_match('%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $url);
}

function getDataFromDomElements($tagName, $attributeName, $dataArr, $url) {
    $html = file_get_contents($url);

    $doc = new DOMDocument();
    @$doc->loadHTML($html);

    $tags = $doc->getElementsByTagName($tagName);
    foreach ($tags as $tag) {
        $scriptData = $tag->getAttribute($attributeName);
        $resIsLink = valid_URL($scriptData);
        if ($resIsLink) {
            $dataArr['link'][] = $scriptData;
        } else {
            $dataArr[$tagName][] = $scriptData;
        }
    }

    return $dataArr;
}

$checkIsUrl = valid_URL($parseUrl);

if ($checkIsUrl) {
    $outputData = [];

    $outputData = getDataFromDomElements(URL_TAG['tagName'], URL_TAG['attributeName'], $outputData, $parseUrl);
    $outputData = getDataFromDomElements(IMG_TAG['tagName'], IMG_TAG['attributeName'], $outputData, $parseUrl);
    $outputData = getDataFromDomElements(SCRIPT_TAG['tagName'], SCRIPT_TAG['attributeName'], $outputData, $parseUrl);

    die(json_encode($outputData));
} else {
    die("Not correct input data!");
}

