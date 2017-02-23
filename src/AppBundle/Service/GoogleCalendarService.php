<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class GoogleCalendarService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createEvent($host_id, $properties)
    {
        // Set google client
        $client = new \Google_Client();

        $em = $this->container->get('doctrine')->getEntityManager();

        // Get $user_auth
        $user = $em->getRepository('AppBundle:User')->findOneById($host_id);
        $user_auth = $user->getGoogleAuth();

        $client->setAccessToken($user_auth);

        $service = new \Google_Service_Calendar($client);

        $event = new \Google_Service_Calendar_Event($properties);

        $event = $service->events->insert('primary', $event, array('sendNotifications' => true));

        return $event->htmlLink;
    }
}
