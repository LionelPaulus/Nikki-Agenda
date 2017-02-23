<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $googleCalendarService = $this->get('app.service.google_calendar_api');
        $datas = [
            'summary' => 'Réunion pyjama',
              'location' => '4 Rue du Progrès',
              'start' => array(
                'dateTime' => '2017-02-23T09:00:00',
                'timeZone' => 'Europe/Berlin',
              ),
              'end' => array(
                'dateTime' => '2017-02-23T17:00:00',
                'timeZone' => 'Europe/Berlin',
              ),
            //   'attendees' => array(
            //     array('email' => 'lpage@example.com'),
            //     array('email' => 'sbrin@example.com'),
            //   ),
        ];
        $calendar_event = $googleCalendarService->createEvent(10, $datas);

        $event = new Event();
        $event->setTeamId(25);
        $event->setCreatorId(10);
        $event->setGoogleCalendarId($calendar_event->id);

        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();

        echo '<pre>';
        var_dump($event);
        echo '</pre>';

        die();
        // replace this example code with whatever you need
        return $this->render('index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}
