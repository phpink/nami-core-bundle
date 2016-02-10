<?php

namespace PhpInk\Nami\CoreBundle\Util;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PhpInk\Nami\CoreBundle\Model\Orm\Analytics\PageAnalytics as OrmPageAnalytics;
use PhpInk\Nami\CoreBundle\Model\Odm\Analytics\PageAnalytics as OdmPageAnalytics;
use PhpInk\Nami\CoreBundle\Model\PageInterface;

class Analytics
{
    /**
     * Register a page hit
     *
     * @param mixed         $em        The database manager.
     * @param string        $ip        The visitor ip.
     * @param string        $userAgent The visitor user-agent.
     * @param PageInterface $page      The page visited.
     *
     * @return void
     */
    public static function registerPageHit($em, $ip, $userAgent, PageInterface $page)
    {
        if ($em instanceof DocumentManager) {
            $analytics = new OdmPageAnalytics($page, $ip, $userAgent);
        } else {
            $analytics = new OrmPageAnalytics($page, $ip, $userAgent);
        }
        try {
            $em->persist($analytics);
            $em->flush();

        } catch (UniqueConstraintViolationException $e) {

        }
    }
}
