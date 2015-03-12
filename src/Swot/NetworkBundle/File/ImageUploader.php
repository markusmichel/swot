<?php

namespace Swot\NetworkBundle\File;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader implements FileUploadInterface {

    /** @var string Absolute path to web directory */
    private $webDir;
    private $uploadDir = 'profileimages/';

    private $file;


    public function __construct($kernelDir, $uploadDir) {
        $this->webDir = $kernelDir . '/../web/';
        $this->uploadDir = $uploadDir;
    }

    private function generateFilename() {
        return uniqid();
    }

    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        $filename = $this->generateFilename() . "." . $this->getFile()->getClientOriginalExtension();

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadDirAbsolute(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->profileImage = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->profileImageFile = null;

        return $filename;
    }

    /**
     * Relative path to the upload directory.
     * @return String
     */
    public function getUploadDirRelative()
    {
        return $this->uploadDir;
    }

    /**
     * Absolute URL to the upload directory.
     * @return String
     */
    public function getUploadDirAbsolute()
    {
        return $this->webDir . $this->uploadDir;
    }

    /**
     * @param UploadedFile $file
     * @return mixed
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
}