<?php

namespace Swot\NetworkBundle\Services;


class CurlManager {

    public function __construct(){

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
                else die('Server responded with code ' . $result->info['http_code']);
            }

            // something went wrong
            // ($result still contains all data that could be gathered)
            // @todo: create exception
            else die('cURL responded with: ' . $result->response[0]);
        });

        return json_decode($response);
    }

}