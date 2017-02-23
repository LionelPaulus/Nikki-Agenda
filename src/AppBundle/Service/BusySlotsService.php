<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Response;

class BusySlotsService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function retrieveBusySlots($start_time, $end_time, $id_user)
    {

        // Set google client
        $client = new \Google_Client();

        $em = $this->container->get('doctrine')->getEntityManager();
        // Get user id
        $user = $em->getRepository('AppBundle:User')->findOneById($id_user);

        // Get user auth
        $user_auth = $user->getGoogleAuth();

        $client->setAccessToken($user_auth);

        // Convert timestamp to google friendly date
        $start_time = date('Y-m-d\TH:i:s', $start_time);
        $end_time = date('Y-m-d\TH:i:s', $end_time);

        $start_time = $start_time.'+01:00';
        $end_time = $end_time.'+01:00';

        // Retrieve calendars from user
        $calendar = new \Google_Service_Calendar($client);

        // Create freebusy request
        $freebusy = new \Google_Service_Calendar_FreeBusyRequest();
        $freebusy->setTimeMin($start_time);
        $freebusy->setTimeMax($end_time);
        $freebusy->setTimeZone('Europe/Berlin');
        $item = new \Google_Service_Calendar_FreeBusyRequestItem();
        $item->setId('primary');
        $freebusy->setItems(array($item));
        $busy_slots = $calendar->freebusy->query($freebusy);
        // Fill events array with busy slots retrieved from user calendar
        $events = array();
        $i = 0;

        // Retrieve busy ranges of time in the events array
        foreach ($busy_slots["calendars"]["primary"]["modelData"]["busy"] as $busy) {
            $events[$i] = $busy;
            $i ++ ;
        }

        $i = 0;

        // Convert events time format to timestamp
        foreach ($events as $event) {
            $events[$i]["start"] = strtotime($event["start"]);
            $events[$i]["end"] = strtotime($event["end"]);
            $i ++;
        }

        return $events;
    }
}
