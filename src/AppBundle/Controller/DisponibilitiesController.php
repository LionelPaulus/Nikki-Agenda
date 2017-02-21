<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DisponibilitiesController extends Controller
{

    /**
     * @Route("/dispos/{start_time}/{end_time}")
     */
    public function findDisposAction($start_time, $end_time)
    {

        $client = $this->container->get('happyr.google.api.client');
        $accessToken = $this->get('session')->get('userGoogleAuth');
        $client->setAccessToken($accessToken);

        $start_time = new \DateTime($start_time, new \DateTimeZone('Europe/Berlin'));
        $end_time = new \DateTime($end_time, new \DateTimeZone('Europe/Berlin'));
        $start_time = date(DATE_ATOM, strtotime($start_time->format('Y-m-d H:i:sP')));
        $end_time = date(DATE_ATOM, strtotime($end_time->format('Y-m-d H:i:sP')));

        $calendar = new \Google_Service_Calendar($client->getGoogleClient());
        $list = $calendar->calendarList->listCalendarList();

        $freebusy = new \Google_Service_Calendar_FreeBusyRequest();
        $freebusy->setTimeMin($start_time);
        $freebusy->setTimeMax($end_time);
        $freebusy->setTimeZone('Europe/Berlin');
        $item = new \Google_Service_Calendar_FreeBusyRequestItem();
        $item->setId('primary');
        $freebusy->setItems(array($item));
        $disponibilities = $calendar->freebusy->query($freebusy);

        return $disponibilities;

    }
}
