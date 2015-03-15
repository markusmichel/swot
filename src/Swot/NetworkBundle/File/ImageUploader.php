<?php

namespace Swot\NetworkBundle\File;


use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader implements FileUploadInterface {

    /** @var string Absolute path to web directory */
    private $webDir;
    private $uploadDir = 'profileimages/';

    private $file;

    private $dataManager;
    private $filterManager;

    public function __construct($kernelDir, $uploadDir, DataManager $dataManager, FilterManager $filterManager) {
        $this->webDir = $kernelDir . '/../web/';
        $this->uploadDir = $uploadDir;

        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
    }

    /**
     * Resizes an image.
     * @param $imagePath Path + filename of the image file to resize
     * @param $filterName Name of the liip imagine filter to apply
     */
    private function resizeImage($imagePath, $filterName) {
        $dataManager   = $this->dataManager;
        $filterManager = $this->filterManager;

        // find the image and determine its type
        $image = $dataManager->find($filterName, $imagePath);

        $response = $filterManager->applyFilter($image, $filterName);
        $thumb = $response->getContent();

        // create and write new image file
        $f = fopen($imagePath, 'w');
        fwrite($f, $thumb);
        fclose($f);
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

        $this->resizeImage($this->getUploadDirRelative() . $filename, 'profile_image');

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