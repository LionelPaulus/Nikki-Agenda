<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DisponibilitiesController extends Controller
{
    // private $accessScope = [
    //   \Google_Service_Calendar::CALENDAR,
    //   \Google_Service_People::CONTACTS_READONLY
    // ];

    /**
     * @Route("/dispos")
     */
    public function findDisposAction()
    {

        $client = $this->container->get('happyr.google.api.client');
        $accessToken = $this->get('session')->get('userGoogleAuth');
        $client->setAccessToken($accessToken);

        $calendar = new \Google_Service_Calendar($client->getGoogleClient());

        // List events
        $calendarId = 'primary';
        $optParams = array(
          'maxResults' => 10,
          'orderBy' => 'startTime',
          'singleEvents' => true,
          'timeMin' => date('c'),
        );

        $listEvents = $calendar->events->listEvents($calendarId, $optParams);
        var_dump($listEvents);
        die();

        // $disponibilities = $this->get('app.mailer');
        // $mailer->send('ryan@foobar.net', ...);
    }
}
