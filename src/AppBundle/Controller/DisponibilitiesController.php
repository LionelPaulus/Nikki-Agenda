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
        $list = $calendar->calendarList->listCalendarList();
        // $start_time = $start_time->format(DateTime::ISO8601);
        // $end_time = $end_time->format(DateTime::ISO8601);
        //
        // echo "<pre>";
        //   var_dump($list);
        // echo "</pre>";
        // die();
        // foreach ($list->getItems() as $calendarListEntry) {
        //     $temp = $calendarListEntry->id;
        //     foreach ($em as $emTemp) {
        //         if ($temp == $emTemp) {
        //             $idArray[$count] = $temp;
        //             //echo $idArray[$count].'<br>';
        //             $count++;
        //         }
        //     }
        // }

        $freebusy = new \Google_Service_Calendar_FreeBusyRequest();
        $freebusy->setTimeMin($start_time);
        $freebusy->setTimeMax($end_time);
        $freebusy->setTimeZone('DE');
        // $freebusy->setItems(array($id_list));
        $item = new \Google_Service_Calendar_FreeBusyRequestItem();
        $item->setId('primary');
        $freebusy->setItems(array($item));
        $disponibilities = $calendar->freebusy->query($freebusy);

        echo "<pre>";
          var_dump($disponibilities);
        echo "</pre>";

        die('ok');
        // return $disponibilities;
        // $disponibilities = $this->get('app.mailer');
        // $mailer->send('ryan@foobar.net', ...);
    }
}
