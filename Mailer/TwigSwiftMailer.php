<?php

namespace PhpInk\Nami\CoreBundle\Mailer;

use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Twig Swiftmailer
 */
class TwigSwiftMailer implements MailerInterface
{
    protected $mailer;
    protected $twig;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $parameters)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->parameters = $parameters;
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['template']['confirmation'];
        $url = str_replace(
            '{token}', $user->getConfirmationToken(),
            $this->parameters['url']['confirmation']
        );
        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );

        return $this->sendMessage(
            $template, $context,
            $this->parameters['from_email']['resetting'],
            $user->getEmail()
        );
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['template']['resetting'];
        $url = str_replace(
            '{token}', $user->getConfirmationToken(),
            $this->parameters['url']['resetting']
        );
//        $url = str_replace(
//            array('{id}', '{token}'),
//            array(
//                $user->getId(),
//                $user->getConfirmationToken()
//            ),
//            $this->parameters['url']['resetting']
//        );

        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );

        return $this->sendMessage(
            $template, $context,
            $this->parameters['from_email']['resetting'],
            $user->getEmail()
        );
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     * @return boolean Mail sent
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        return (boolean) $this->mailer->send($message);
    }
}
