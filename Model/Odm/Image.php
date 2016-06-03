<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\Serializer\Annotation as JMS;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;
use PhpInk\Nami\CoreBundle\Model\ImageInterface;
use PhpInk\Nami\CoreBundle\Util\Globals;
use PhpInk\Nami\CoreBundle\Exception\EmptyFileException;
use PhpInk\Nami\CoreBundle\Exception\UploadDirException;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Document\Image
 *
 * @ODM\MappedSuperClass
 * @ODM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Image extends Core\Document implements ImageInterface
{
    const DEFAULT_SUBFOLDER = 'other';

    /**
     * Primary Key
     * @var string
     * @ODM\Id
     * @JMS\Expose
     */
    protected $id;

    /**
     * Unmapped property to handle file uploads
     *
     * @var UploadedFile
     */
    private $file;

    /**
     * @var string
     * @ODM\String
     */
    private $name;

    /**
     * @var string
     * @ODM\String
     */
    private $filename;

    /**
     * @var string
     * @ODM\String
     */
    private $folder;

    /**
     * Is master?
     *
     * @var bool
     * @ODM\Boolean
     * @JMS\Expose
     */
    protected $master = false;

    /**
     * Unmapped property to handle thumbs
     * @var array
     */
    protected $thumbs;

    /**
     * @var array
     * @JMS\Expose
     * @JMS\Accessor("getReferences")
     * @JMS\MaxDepth(2)
     */
    protected $_references = array();

    /**
     * Image constructor
     *
     * @param string $name
     * @param string $folder
     * @param bool   $master
     */
    public function __construct($name = null, $folder = null, $master = null)
    {
        $this->folder = self::DEFAULT_SUBFOLDER;
        if (!is_null($name)) {
            $this->setName($name);
        }
        if (!is_null($folder)) {
            $this->setFolder($folder);
        }
        if (!is_null($master)) {
            $this->setMaster($master);
        }
    }

    /**
     * Get the value of id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id.
     *
     * @param string
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     * @throws EmptyFileException when no file has been uploaded
     * @return $this
     */
    public function upload()
    {
        // the file property cannot be empty
        if (null === $this->getFile()) {
            throw new EmptyFileException("File is empty");
        }

        // Sanitize the filename
        $this->filename = Urlizer::transliterate(
            $this->getFile()->getClientOriginalName()
        );

        // check upload dir
        $this->checkUploadDir();

        // change filename if a file with the same name exists
        if (file_exists($this->getPath())) {
            $i = 1;
            $originalFilename = $this->filename;
            do {
                $this->filename = $i++. '_'. $originalFilename;
                if ($i > 3) {
                    $this->filename = uniqid();
                }
            } while (
                file_exists($this->getPath())
            );
        }

        // move the file to the upload dir
        $this->getFile()->move(
            $this->getDirectoryPath(), $this->filename
        );

        // file property cleaned, not needed anymore
        $this->setFile(null);

        return $this;
    }

    /**
     * Checks if the upload dir
     * exists and is writable
     *
     * @throws UploadDirException
     */
    public function checkUploadDir()
    {
        if (!file_exists(Globals::getUploadDir())) {
            throw new UploadDirException(
                sprintf("Upload dir does not exists [%s]", Globals::getUploadDir())
            );
        }
        if (!is_writable(Globals::getUploadDir())) {
            throw new UploadDirException(
                sprintf("Upload dir is not writable [%s]", Globals::getUploadDir())
            );
        }
        // Creates the sub-folder if it does not exist
        if (!file_exists($this->getDirectoryPath())) {
            mkdir($this->getDirectoryPath());
        }
    }

    /**
     * Delete the file of the upload dir
     * when it is deleted from the database
     *
     * @ODM\PostRemove()
     */
    public function onPostRemove()
    {
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set folder
     *
     * @param string $folder
     * @return $this
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Get the image directory path
     *
     * @param boolean $absolute Absolute path
     * @return string
     */
    public function getDirectoryPath($absolute = true)
    {
        return Globals::getUploadDir($absolute) .
        ($this->folder ?
            DIRECTORY_SEPARATOR . $this->folder : '');
    }

    /**
     * Get the image path
     *
     * @param boolean $absolute Absolute path
     * @return string
     */
    public function getPath($absolute = true)
    {
        return $this->getDirectoryPath($absolute) .
            DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * Get the image url
     *
     * @param boolean $absolute Absolute url
     * @return string
     * @JMS\VirtualProperty
     * @JMS\SerializedName("url")
     */
    public function getUrl($absolute = true)
    {
        $url =  Globals::getUploadDir(false) . '/' .
            $this->folder . ($this->folder ? '/' : '') .
            $this->filename;

        if ($absolute) {
            $url = Globals::getHost() . $url;
        }
        return $url;
    }

    /**
     * Get the value of master.
     *
     * @return boolean
     */
    public function isMaster()
    {
        return $this->master;
    }

    /**
     * Set the value of master.
     *
     * @param boolean
     * @return $this
     */
    public function setMaster($master)
    {
        $this->master = (boolean) $master;

        return $this;
    }

    /**
     * Generate thumbs section
     * with LiipImageService
     *
     * @param CacheManager $liipManager
     * @return $this
     */
    public function generateThumbs(CacheManager $liipManager)
    {
        $path = $this->getPath(false);
        $this->thumbs = array(
          'preview' => $liipManager->getBrowserPath($path, 'preview'),
          'category'  => $liipManager->getBrowserPath($path, 'category')
        );
        return $this;
    }

    /**
     * Add thumbs section with urls
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("thumbs")
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @return array
     */
    public function getReferences(UserInterface $user = null)
    {
        if (empty($this->_references)) {
            $this->_references = array();
            if ($user && $user->isAdmin()) {
                $this->_references['createdBy'] = null;
                if ($this->createdBy) {
                    $this->_references['createdBy'] = $this->createdBy;
                }
            }
        }
        return $this->_references;
    }

    public function __toString()
    {
        return (string) $this->getUrl();
    }
}
