<?php


namespace Swot\NetworkBundle\Services;


class QrReader {

    private $binDir;
    private $filePath;

    /**
     * Reads QrCode and returns its value.
     */
    public function readQrCode($filePath){

        $this->filePath = $filePath;

        //@TODO read dynamically
        $this->filePath = $this->binDir . "exampleQR.png";

        $command = "java -jar ". $this->binDir . "qr.jar " . $this->filePath;
        return exec($command);
    }

    public function __construct($kernelDir){
        $this->binDir = $kernelDir . '/../bin/';
    }
}