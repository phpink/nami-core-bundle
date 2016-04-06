<?php

namespace PhpInk\Nami\CoreBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

/**
 * Image interface
 */
interface ImageInterface
{
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null);

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile();

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload();

    /**
     * Checks if the upload dir
     * exists and is writable
     *
     * @throws UploadDirException
     */
    public function checkUploadDir();

    /**
     * Delete the file of the upload dir
     * when it is deleted from the database
     *
     * Db PostRemove()vHOOK
     */
    public function onPostRemove();

    /**
     * Set filename
     *
     * @param string $filename
     * @return ImageInterface
     */
    public function setFilename($filename);

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename();

    /**
     * Set name
     *
     * @param string $name
     * @return ImageInterface
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set folder
     *
     * @param string $folder
     * @return ImageInterface
     */
    public function setFolder($folder);

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder();

    /**
     * Get the image directory path
     *
     * @param boolean $absolute Absolute path
     * @return string
     */
    public function getDirectoryPath($absolute = true);

    /**
     * Get the image path
     *
     * @param boolean $absolute Absolute path
     * @return string
     */
    public function getPath($absolute = true);

    /**
     * Get the image url
     *
     * @param boolean $absolute Absolute url
     * @return string
     */
    public function getUrl($absolute = true);

    /**
     * Get the value of master.
     *
     * @return boolean
     */
    public function isMaster();

    /**
     * Set the value of master.
     *
     * @param boolean
     * @return ImageInterface
     */
    public function setMaster($master);

    /**
     * Generate thumbs section
     * with LiipImageService
     *
     * @param CacheManager $liipManager
     * @return ImageInterface
     */
    public function generateThumbs(CacheManager $liipManager);

    /**
     * Add thumbs section with urls
     */
    public function getThumbs();

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @return array
     */
    public function getReferences(UserInterface $user = null);
}
