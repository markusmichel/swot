<?php

namespace Swot\NetworkBundle\Services;


use Swot\NetworkBundle\Exception\ThingIsUnavailableException;
use Swot\NetworkBundle\Exception\ThingSendFailureException;

class CurlManager {


    private $uploadDir;

    public function __construct($kernelDir){
        $this->uploadDir = $kernelDir . '/../web/uploads/profileimages/';
    }

    /**
     * Gets a response via cURL.
     * @param $url String URL for cURL request
     * @return mixed content of the cURL reponse
     */
    public function getCurlResponse($url){

        $response = null;
        $curl = new \Zebra_cURL();
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

        return json_decode($response);
    }

    /**
     * Gets a image via response via cURL.
     * @param $url String URL for cURL request
     * @return mixed content of the cURL reponse
     */
    public function getCurlImageResponse($url){

        $curl = new \Zebra_cURL();
        $fullPath = $this->uploadDir . basename($url);

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

}