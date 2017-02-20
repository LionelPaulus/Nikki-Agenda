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

        $calendar = new \Google_Service_Calendar($client->getGoogleClient());
        $id_list = $calendar->calendarList->listCalendarList();
        // $start_time = $start_time->format(DateTime::ISO8601);
        // $end_time = $end_time->format(DateTime::ISO8601);


        $freebusy = new \Google_Service_Calendar_FreeBusyRequest();
        $freebusy->setTimeMin($start_time);
        $freebusy->setTimeMax($end_time);
        $freebusy->setTimeZone('de');
        $freebusy->setItems($id_list);
        $disponibilities = $calendar->freebusy->query($freebusy);
        echo "<pre>";
          var_dump($disponibilities); // or var_dump($data);
        echo "</pre>";
        die('ok');
        // return $disponibilities;
        // $disponibilities = $this->get('app.mailer');
        // $mailer->send('ryan@foobar.net', ...);
    }
}
