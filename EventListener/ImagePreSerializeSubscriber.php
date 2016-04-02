<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpInk\Nami\CoreBundle\Model\ImageInterface;
use PhpInk\Nami\CoreBundle\Util\Globals;

/**
 * JMS PreSerialize listener
 * to add thumbs references on Images
 */
class ImagePreSerializeSubscriber implements EventSubscriberInterface
{
    /**
     * @var CacheManager
     */
    private $liipManager;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * Constructor
     *
     * @param CacheManager $liipManager
     * @param string       $uploadDir
     */
    public function __construct(CacheManager $liipManager, $uploadDir)
    {
        $this->liipManager = $liipManager;
        $this->uploadDir = $uploadDir;
    }


    /**
     * {@inheritDoc}
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $model = $event->getObject();
        Globals::setUploadDir($this->uploadDir);
        if ($model instanceof ImageInterface) {
            $model->generateThumbs(
                $this->liipManager
            );
        }
    }


    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }
}
