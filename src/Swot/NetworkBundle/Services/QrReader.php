<?php


namespace Swot\NetworkBundle\Services;


class QrReader {

    private $binDir;
    private $filePath;

    /**
     * @param $filePath String filepath to the picture
     * @return string the content of the qr code
     */
    public function readQrCode($filePath){

        $this->filePath = $filePath;
        $command = "java -jar ". $this->binDir . "qr.jar " . $this->filePath;
        return exec($command);
    }

    public function __construct($kernelDir){
        $this->binDir = $kernelDir . '/../bin/';
    }
}