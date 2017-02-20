<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DisponibilitiesController extends Controller
{
    /**
     * @Route("/dispos")
     */
    public function findDisposAction()
    {
        // die('ok');
        $accessToken = $this->get('session')->get('userGoogleAuth');
        // die($accessToken);
        // $client = $this->container->get('happyr.google.api.client');
        // die($client);
        // $client->setAccessToken($accessToken);
        // $calendar = new \Google_Service_Calendar($client->getGoogleClient());
        // die($calendar);
        // $disponibilities = $this->get('app.mailer');
        // $mailer->send('ryan@foobar.net', ...);
    }
}
