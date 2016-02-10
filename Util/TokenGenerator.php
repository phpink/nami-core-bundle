<?php

namespace PhpInk\Nami\CoreBundle\Util;

use Psr\Log\LoggerInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    private $logger;
    private $useOpenSsl;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        // determine whether to use OpenSSL
        if (defined('PHP_WINDOWS_VERSION_BUILD') && version_compare(PHP_VERSION, '5.3.4', '<')) {
            $this->useOpenSsl = false;
        } elseif (!function_exists('openssl_random_pseudo_bytes')) {
            if (null !== $this->logger) {
                $this->logger->notice('It is recommended that you enable the "openssl" extension for random number generation.');
            }
            $this->useOpenSsl = false;
        } else {
            $this->useOpenSsl = true;
        }
    }

    public function generateToken()
    {
        return rtrim(
            strtr(
                base64_encode($this->getRandomNumber()),
                array(
                    '+/' => '-_',
                    '+' => '-',
                    '|' => strval(rand(0, 9)),
                    '/' => strval(rand(0, 9))
                )
            ), '='
        );
    }

    private function getRandomNumber()
    {
        $nbBytes = 32;

        // try OpenSSL
        if ($this->useOpenSsl) {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);

            if (false !== $bytes && true === $strong) {
                return $bytes;
            }

            if (null !== $this->logger) {
                $this->logger->info('OpenSSL did not produce a secure random number.');
            }
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}
