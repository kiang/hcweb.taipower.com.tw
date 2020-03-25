<?php
// layer list https://hcweb.taipower.com.tw/ltflgeoserver/ows?service=wfs&version=1.0.0&request=GetCapabilities
// get json https://hcweb.taipower.com.tw/ltflgeoserver/ows?service=wfs&version=1.0.0&request=getFeature&srsName=EPSG:4326&outputFormat=application/json&typeName=postgis%3Afu01006
$listFile = dirname(__DIR__) . '/list.xml';
$xml = exec("curl --insecure 'https://hcweb.taipower.com.tw/ltflgeoserver/ows?service=wfs&version=1.0.0&request=GetCapabilities' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: en-US,en;q=0.5' -H 'Connection: keep-alive' -H 'Upgrade-Insecure-Requests: 1' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache'");
$xml = trim($xml);
if(substr($xml, 0, 11) === '</Abstract>') {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <WFS_Capabilities version="1.0.0" xmlns="http://www.opengis.net/wfs" xmlns:der="http://www.ximple.com.tw/der" xmlns:xtpc="http://tpc.ximple.com.tw/geodmms" xmlns:it.geosolutions="http://www.geo-solutions.it" xmlns:topp="http://www.openplans.org/topp" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wfs http://hcweb.taipower.com.tw:80/ltflgeoserver/schemas/wfs/1.0.0/WFS-capabilities.xsd"><Service><Name>My GeoServer WFS</Name><Title>My GeoServer WFS</Title><Abstract>
    This is a description of your Web Feature Server.
    
    The GeoServer is a full transactional Web Feature Server, you may wish to limit
    GeoServer to a Basic service level to prevent modificaiton of your geographic
    data.' . $xml;
}
file_put_contents($listFile, $xml);
$xml = simplexml_load_string($xml);
foreach($xml->FeatureTypeList->FeatureType AS $ft) {
    $jsonFile = dirname(__DIR__) . '/json/' . (string)$ft->Title . '.json';
    $url = urlencode((string)$ft->Name);
    $json = exec("curl --insecure 'https://hcweb.taipower.com.tw/ltflgeoserver/ows?service=wfs&version=1.0.0&request=getFeature&srsName=EPSG:4326&outputFormat=application/json&typeName={$url}' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: en-US,en;q=0.5' --compressed -H 'Connection: keep-alive' -H 'Upgrade-Insecure-Requests: 1' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache'");
    file_put_contents($jsonFile, $json);
}