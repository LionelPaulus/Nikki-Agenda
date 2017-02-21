<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DisponibilitiesController extends Controller
{

    /**
     * @Route("/dispos/{start_time}/{end_time}")
     */
    public function findDisposAction($start_time, $end_time)
    {

        // Set google client
        $client = $this->container->get('happyr.google.api.client');
        // Set access token
        $accessToken = $this->get('session')->get('userGoogleAuth');
        $client->setAccessToken($accessToken);

        // Format datetime so it is usable by Google Freebusy api

        $start_time = new \DateTime($start_time, new \DateTimeZone('Europe/Berlin'));
        $end_time = new \DateTime($end_time, new \DateTimeZone('Europe/Berlin'));

        $start_time = date('c', strtotime($start_time->format('Y-m-d H:i:sP')));
        $end_time = date('c', strtotime($end_time->format('Y-m-d H:i:sP')));

        // Retrieve calendars from user
        $calendar = new \Google_Service_Calendar($client->getGoogleClient());
        // $list = $calendar->calendarList->listCalendarList();

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

        return new JsonResponse($events);
    }
}
