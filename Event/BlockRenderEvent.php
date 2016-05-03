<?php namespace PhpInk\Nami\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Controller\FrontendController;

/**
 * The block.render event is dispatched each time a block is rendered
 * in the system.
 */
class BlockRenderEvent extends Event
{
    const NAME = 'nami.block.render';

    protected $block;
    protected $request;

    public function __construct(BlockInterface $block, Request $request)
    {
        $this->block = $block;
        $this->request = $request;
    }

    public function getBlock()
    {
        return $this->block;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
