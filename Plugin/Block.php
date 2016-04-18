<?php

namespace PhpInk\Nami\CoreBundle\Plugin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;

/**
 * ContactForm block plugin
 *
 * @package NamiPlugin
 */
class Block
{
    /**
     * @var Controller
     */
    protected $controller;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var BlockInterface
     */
    protected $block;

    /**
     * Plugin output
     * @var string
     */
    protected $output;

    public function __construct(Controller $controller, Request $request, BlockInterface $block)
    {
        $this->setController($controller);
        $this->setRequest($request);
        $block->setPlugin($this);
        $this->setBlock($block);
    }

    /**
     * Get the value of controller
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the value of controller
     * @param Controller $controller
     * @return Plugin
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Get the value of request
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the value of request
     * @param Request $request
     * @return Plugin
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get the value of block
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set the value of block
     * @param BlockInterface $block
     * @return Plugin
     */
    public function setBlock($block)
    {
        $this->block = $block;
        return $this;
    }

    /**
     * Get the value of output
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set the value of output
     * @param string $output
     * @return Plugin
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }
}
