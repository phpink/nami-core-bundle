<?php

namespace PhpInk\Nami\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use PhpInk\Nami\CoreBundle\Model\ImageInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Page interface
 */
interface PageInterface
{
    /**
     * Set the value of active.
     *
     * @param boolean $active
     * @return PageInterface
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
     * @return PageInterface
     */
    public function setTitle($title);

    /**
     * Get the value of slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Set slug
     *
     * @param string $slug
     * @return PageInterface
     */
    public function setSlug($slug);

    /**
     * Get the value of header.
     *
     * @return string
     */
    public function getHeader();

    /**
     * Set the value of header.
     *
     * @param string $header
     * @return PageInterface
     */
    public function setHeader($header);

    /**
     * Get the value of metaKeywords.
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Set the value of metaKeywords.
     *
     * @param string $keywords
     * @return PageInterface
     */
    public function setMetaKeywords($keywords);

    /**
     * Get the value of metaDescription.
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set the value of metaDescription.
     *
     * @param string $description
     * @return PageInterface
     */
    public function setMetaDescription($description);

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
     * @return PageInterface
     */
    public function setContent($content);

    /**
     * Remove a block
     *
     * @param BlockInterface $block
     * @return PageInterface
     */
    public function removeBlock(BlockInterface $block);

    /**
     * Add a block
     *
     * @param BlockInterface $block
     * @return PageInterface
     */
    public function addBlock(BlockInterface $block);

    /**
     * Returns the blocks
     *
     * @return Collection
     */
    public function getBlocks();

    /**
     * Add a block
     *
     * @param Collection $blocks
     * @return PageInterface
     */
    public function setBlocks(Collection $blocks);

    /**
     * Get the value of template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Set the value of template.
     *
     * @param string $template
     * @return PageInterface
     */
    public function setTemplate($template);

    /**
     * Get the id of the page background.
     *
     * @return string
     */
    public function getBackgroundId();

    /**
     * Get the url of the page background.
     *
     * @return string
     */
    public function getBackgroundUrl();

    /**
     * Set background Page (one to one).
     *
     * @param ImageInterface $background
     * @return PageInterface
     */
    public function setBackground(ImageInterface $background = null);

    /**
     * Get background Page (one to one).
     *
     * @return ImageInterface
     */
    public function getBackground();

    /**
     * Get the id of the category page.
     *
     * @return string
     */
    public function getCategoryId();

    /**
     * Set category Page (one to one).
     *
     * @param CategoryInterface $category
     * @return PageInterface
     */
    public function setCategory(CategoryInterface $category = null);

    /**
     * Get category Page (one to one).
     *
     * @return PageInterface
     */
    public function getCategory();

    /**
     * Get the value of backgroundColor.
     *
     * @return string
     */
    public function getBackgroundColor();

    /**
     * Set the value of backgroundColor.
     *
     * @param string $backgroundColor
     * @return PageInterface
     */
    public function setBackgroundColor($backgroundColor);

    /**
     * Get the value of borderColor.
     *
     * @return string
     */
    public function getBorderColor();

    /**
     * Set the value of borderColor.
     *
     * @param string $borderColor
     * @return PageInterface
     */
    public function setBorderColor($borderColor);

    /**
     * Get the value of footerColor.
     *
     * @return string
     */
    public function getFooterColor();

    /**
     * Set the value of footerColor.
     *
     * @param string $footerColor
     * @return PageInterface
     */
    public function setFooterColor($footerColor);

    /**
     * Get the value of negativeText.
     *
     * @return boolean
     */
    public function isNegativeText();

    /**
     * Set the value of negativeText.
     *
     * @param boolean $negativeText
     * @return PageInterface
     */
    public function setNegativeText($negativeText);

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @param array     $groups
     * @return array
     */
    public function getReferences(UserInterface $user = null, $groups = array());

    public function __toString();
}
