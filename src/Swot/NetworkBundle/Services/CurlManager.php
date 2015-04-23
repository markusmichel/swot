<?php

namespace Swot\NetworkBundle\Services;


use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Exception\ThingIsUnavailableException;
use Swot\NetworkBundle\Exception\ThingSendFailureException;
use League\Url\Url;

class CurlManager {


    private $uploadDir;
    private $apiInformationEndpoint;

    public function __construct($kernelDir, $apiInformationEndpoint){
        $this->uploadDir = $kernelDir . '/../web/uploads/profileimages/';
        $this->apiInformationEndpoint = $apiInformationEndpoint;
    }

    /**
     * Gets a response via cURL.
     * @param $url String URL for cURL request
     * @param $decodeJson Boolean Determines if the JSON string should be decoded or not
     * @param $accessToken String The accesstoken to authenticate with the thing
     * @param $networkToken String The networktoken used to authenticate with the thing in certain situations
     * @return mixed content of the cURL reponse
     */
    public function getCurlResponse($url, $decodeJson, $accessToken = "", $networkToken = ""){

        $response = null;
        $curl = new \Zebra_cURL();

        // put the accesstoken and if needed the networktoken in the HTTP-Header
        if($networkToken != "")
            $token = array("accesstoken: " . $accessToken, "networktoken: " . $networkToken);
        else if ($accessToken == "" && $networkToken != "")
            $token = array("networktoken: " . $networkToken);
        else
            $token = array("accesstoken: " . $accessToken);

        $curl->option(CURLOPT_HTTPHEADER, $token);

        $curl->get($url, function($result) use (&$response) {
            // everything went well at cURL level
            if ($result->response[1] == CURLE_OK) {

                // if server responded with code 200 (meaning that everything went well)
                // see http://httpstatus.es/ for a list of possible response codes
                if ($result->info['http_code'] == 200) {

                    $response = $result->body;
                    return $response;

                }
                // @todo: create exception
                //else die('Server responded with code ' . $result->info['http_code']);
                else throw new ThingSendFailureException("Something went wrong. Thing responded with: " . $result->info['http_code']);
            }

            // something went wrong
            // ($result still contains all data that could be gathered)
            // @todo: create exception
            else throw new ThingIsUnavailableException('Thing is unavailable. cURL responded with: ' . $result->response[0]);
        });

        if($decodeJson) return json_decode($response);
        else return $response;
    }

    /**
     * Gets a image via response via cURL.
     * @param $url String URL for cURL request
     * @return mixed content of the cURL reponse
     */
    public function getCurlImageResponse($url, $accessToken){

        $curl = new \Zebra_cURL();
        $fullPath = $this->uploadDir . basename($url);

        // put the accesstoken in the HTTP-Header
        $token = array("accesstoken: " . $accessToken);
        $curl->option(CURLOPT_HTTPHEADER, $token);

        $curl->download($url, $this->uploadDir, function($result) use (&$fullPath){
            // everything went well at cURL level
            if ($result->response[1] == CURLE_OK) {

                // if server responded with code 200 (meaning that everything went well)
                // see http://httpstatus.es/ for a list of possible response codes
                if ($result->info['http_code'] == 200) {
                        //print_r("<pre>");
                        //print_r($result);
                }
                // @todo: create exception
                //else die('Server responded with code ' . $result->info['http_code']);
                else throw new ThingSendFailureException("Something went wrong. Thing responded with: " . $result->info['http_code']);
            }

            // something went wrong
            // ($result still contains all data that could be gathered)
            // @todo: create exception
            //else die('cURL responded with: ' . $result->response[0]);
            else throw new ThingIsUnavailableException('Thing is unavailable. cURL responded with: ' . $result->response[0]);
        });

        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        rename($fullPath, $this->uploadDir . $newName);

        return $newName;
    }

    public function getThingStatus(Thing $thing) {
        $baseUrl = $thing->getBaseApiUrl();

        $informationUrl = $baseUrl . $this->apiInformationEndpoint;
        $formattedUrl = URL::createFromUrl($informationUrl);
        $informationData = $this->getCurlResponse($formattedUrl->__toString(), false, $thing->getReadToken());

        return $informationData;
    }

}