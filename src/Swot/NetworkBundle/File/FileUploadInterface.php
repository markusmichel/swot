<?php

namespace Swot\NetworkBundle\File;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploadInterface {

    public function upload();

    /**
     * Relative path to the upload directory.
     * @return String
     */
    public function getUploadDirRelative();

    /**
     * Absolute URL to the upload directory.
     * @return String
     */
    public function getUploadDirAbsolute();

    /**
     * @param UploadedFile $file
     * @return mixed
     */
    public function setFile(UploadedFile $file);

    /**
     * @return UploadedFile
     */
    public function getFile();
}