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

        // List events
        $calendarId = 'primary';

        // $optParams = array(
        //   'timeMin' => $start_time,
        //   'timeMax' => $end_time,
        //   'timeZone' => 'de',
        //   'items'=> [
        //     [
        //       'id' => $id_list,
        //     ]
        //   ],
        // );

        // $optParams = new {
        //   "timeMin": $start_time,
        //   "timeMax": $end_time,
        //   "timeZone": 'de',
        //   "items": [
        //     {
        //       "id": $id_list,
        //     }
        //   ]
        //   };

        $optParams = (object)[
            'timeMin' => $start_time,
            'timeMax' => $end_time,
            'timeZone' => 'de',
            'items'=> [
              [
                'id' => $id_list,
              ]
            ],
        ];
        $freebusy = new Google_FreeBusyRequest();

        $disponibilities = $calendar->freebusy($optParams);

        // $disponibilities = $this->get('app.mailer');
        // $mailer->send('ryan@foobar.net', ...);
    }
}
