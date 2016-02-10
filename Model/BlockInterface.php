<?php

namespace PhpInk\Nami\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Block interface
 */
interface BlockInterface
{
    /**
     * Fill null collection properties
     * DB onPostLoad HOOK
     */
    public function initialize();

    /**
     * Set the value of active.
     *
     * @param boolean $active
     * @return BlockInterface
     */
    public function setActive($active);

    /**
     * Get the value of active.
     *
     * @return boolean
     */
    public function isActive();

    /**
     * Get the value of title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set the value of title.
     *
     * @param string $title
     * @return BlockInterface
     */
    public function setTitle($title);

    /**
     * Get the value of content.
     *
     * @return string
     */
    public function getContent();

    /**
     * Set the value of content.
     *
     * @param string $content
     * @return BlockInterface
     */
    public function setContent($content);

    /**
     * Get the value of type.
     *
     * @return string
     */
    public function getType();

    /**
     * Set the value of type.
     *
     * @param string $type
     * @return BlockInterface
     */
    public function setType($type);

    /**
     * Remove a block image
     *
     * @param ImageInterface $image
     * @return BlockInterface
     */
    public function removeImage(ImageInterface $image);

    /**
     * Add a block image
     *
     * @param ImageInterface $image
     * @return BlockInterface
     */
    public function addImage(ImageInterface $image);

    /**
     * Returns the block images
     *
     * @return ArrayCollection
     */
    public function getImages();

    /**
     * Returns bool if the block has images
     *
     * @return ArrayCollection
     */
    public function hasImages();

    /**
     * Returns first image url
     *
     * @return string
     */
    public function getFirstImage();

    /**
     * Returns first image url
     *
     * @return string
     */
    public function getFirstImageUrl();

    /**
     * Returns first image url
     *
     * @return string
     */
    public function getFirstImageName();

    /**
     * Returns the block images ID
     *
     * @return array
     */
    public function getImagesId();

    /**
     * Get the value of template.
     *
     * @return mixed
     */
    public function getTemplate();

    /**
     * Set the value of template.
     *
     * @param mixed $template
     * @return BlockInterface
     */
    public function setTemplate($template);

    /**
     * Get the value of page.
     *
     * @return PageInterface
     */
    public function getPage();

    /**
     * Get the value of page.
     *
     * @return int|string|null
     */
    public function getPageId();

    /**
     * Set the value of page.
     *
     * @param Page $page
     * @return BlockInterface
     */
    public function setPage($page);

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @param array $groups
     * @return array
     */
    public function getReferences(UserInterface $user = null, $groups = array());

    public function __toString();
}
