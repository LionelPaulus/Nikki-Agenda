<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class EmailService
{
    private $container;
    protected $mailer;

    public function __construct(Container $container, \Swift_Mailer $mailer)
    {
        $this->container = $container;
        $this->mailer = $mailer;
    }

    public function invitationEmail($recipientEmail, $firstName, $lastName, $teamName, $email)
    {
        // Sujet
        $subject = $firstName.' invited you to Nikki Agenda';

        // message
        $message = $this->container->get('templating')->render(
            'AppBundle:Emails:invitation.html.twig',
            array(
                'firstName' => $firstName,
                'lastName' => $lastName,
                'teamName' => $teamName,
                'email' => $email,
            )
        );

        $headers = 'From: Nikki Agenda <samantha@nikkiagenda.com>' . "\r\n";

        // Send invitation email
        $mail = mail($recipientEmail, $subject, $message, $headers);
        if($mail){
            echo "sent";
        }else {
            echo "not sent :(";
        }
        die();

        $message = \Swift_Message::newInstance()
        ->setSubject($firstName.' invited you to Nikki Agenda')
        ->setFrom('nikkiagenda@lionelpaul.us')
        ->setTo($recipientEmail)
        ->setBody(
            $this->container->get(
                // app/Resources/views/Emails/registration.html.twig
                'Emails/invitation.html.twig',
                array(
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'teamName' => $teamName,
                    'email' => $email,
                )
            ),
            'text/html'
        );

        $send = $this->mailer->send($message);

        if (!$this->mailer->send($message, $failures)) {
            echo "Fail:<br>";
            print_r($failures);
        }

        return $send;
    }
}
