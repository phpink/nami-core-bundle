<?php

namespace PhpInk\Nami\CoreBundle\Mailer;

use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * PhpInk API MailerInterface
 */
interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function sendConfirmationEmailMessage(UserInterface $user);

    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function sendResettingEmailMessage(UserInterface $user);
}
