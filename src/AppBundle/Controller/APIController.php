<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Team_Members;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIController extends Controller
{
    public function __construct()
    {
        $this->response = new \stdClass();
        $this->response->code = "200";
        $this->response->message = "All good.";
        $this->response->response = "";
    }

    /**
     * @Route("/api/createEvent", name="createEvent")
     */
    public function createEvent(Request $request)
    {
        // Check if POST has data
        if (empty($_POST)) {
            $this->response->code = "404";
            $this->response->message = "No POST content received";
            return new JsonResponse($this->response);
        }

        // // Check if user is logged
        // if (empty($request->getSession()->get('userId'))) {
        //     $this->response->code = "401";
        //     $this->response->message = "Unauthorized";
        //     return new JsonResponse($this->response);
        // }

        $request->getSession()->set('userId', 10);

        // Check data sent
        $datas = [
            "teamId",
            "eventTitle",
            "fromDate",
            "toDate"
        ];
        foreach ($datas as $data) {
            if (empty($_POST[$data])) {
                $this->response->code = "404";
                $this->response->message = "Missing ".$data." value or NAN";
                return new JsonResponse($this->response);
            }
        }

        // Convert timestamp to google friendly date
        $_POST["fromDate"] = date('Y-m-d\TH:i:s', $_POST["fromDate"]);
        $_POST["toDate"] = date('Y-m-d\TH:i:s', $_POST["toDate"]);

        // Get team members
        $attendees = [];
        $em = $this->getDoctrine()->getEntityManager();
        $team_members = $em->getRepository('AppBundle:Team_Members')->findByTeamId($_POST["teamId"]);
        foreach ($team_members as $member) {
            $member_details = $em->getRepository('AppBundle:User')->findOneById($member->getUserId());
            if ($request->getSession()->get('userEmail') != $member_details->getEmail()) {
                array_push($attendees, array(
                    "email" => $member_details->getEmail()
                ));
            }
        }

        // Location security
        if (empty($_POST["location"])) {
            $_POST["location"] = "";
        }

        // Create the event
        $event_data = [
            'summary' => $_POST["eventTitle"],
              'location' => $_POST["location"],
              'start' => array(
                'dateTime' => $_POST["fromDate"],
                'timeZone' => 'Europe/Berlin',
              ),
              'end' => array(
                'dateTime' => $_POST["toDate"],
                'timeZone' => 'Europe/Berlin',
              ),
              'attendees' => $attendees,
        ];

        $googleCalendarService = $this->get('app.service.google_calendar_api');
        $calendar_event = $googleCalendarService->createEvent($request->getSession()->get('userId'), $event_data);

        try {
            // Create the event
            $event = new Event();
            $event->setTeamId($_POST["teamId"]);
            $event->setCreatorId($request->getSession()->get('userId'));
            $event->setGoogleCalendarId($calendar_event->id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->response->response = true;

            return new JsonResponse($this->response);
        } catch (Exception $e) {
            $this->response->code = "500";
            $this->response->message = "Error: ".$e;
            return new JsonResponse($this->response);
        }
    }

    /**
     * @Route("/api/getSlots", name="getSlots")
     */
    public function getSlots(Request $request)
    {
        // Check if POST has data
        if (empty($_POST)) {
            $this->response->code = "404";
            $this->response->message = "No POST content received";
            return new JsonResponse($this->response);
        }

        // Check if user is logged
        if (empty($request->getSession()->get('userId'))) {
            $this->response->code = "401";
            $this->response->message = "Unauthorized";
            return new JsonResponse($this->response);
        }

        // Check data sent
        $datas = [
            "teamId",
            "fromDate",
            "toDate",
            "duration"
        ];
        foreach ($datas as $data) {
            if ((empty($_POST[$data]))||(is_nan($_POST[$data]))) {
                $this->response->code = "404";
                $this->response->message = "Missing ".$data." value or NAN";
                return new JsonResponse($this->response);
            }
        }

        try {
            $fakeDates = [
                "events" => []
            ];
            array_push($fakeDates["events"], [
                "name" => "lundi 19 - 10h",
                "fromDate" => "9999",
                "toDate" => "9999",
            ]);
            array_push($fakeDates["events"], [
                "name" => "mardi 20 - 15h",
                "fromDate" => "9999",
                "toDate" => "9999",
            ]);
            array_push($fakeDates["events"], [
                "name" => "mercredi 20 - 15h",
                "fromDate" => "9999",
                "toDate" => "9999",
            ]);

            $this->response->response = $fakeDates;

            return new JsonResponse($this->response);
        } catch (Exception $e) {
            $this->response->code = "500";
            $this->response->message = "Error: ".$e;
            return new JsonResponse($this->response);
        }
    }

    /**
     * @Route("/api/getMembersSuggestions", name="getMembersSuggestions")
     */
    public function getMembersSuggestions(Request $request)
    {
        $session = $request->getSession();

        if (empty($session->get('userGoogleAuth'))) {
            // Check if user is logged, if not redirect to homepage
            return $this->redirectToRoute('homepage');
        } else {
            // User is logged

            $contactsSuggestions = [];

            // Get all registered users
            $em = $this->getDoctrine()->getEntityManager();
            $users = $em->getRepository('AppBundle:User')->findAll();
            foreach ($users as $user) {
                array_push($contactsSuggestions, $user->getEmail());
            }

            // Get user Google Contacts emails
            $googleContactsService = $this->get('app.service.google_contacts_api');
            $googleContacts = $googleContactsService->getAllEmails($session->get('userGoogleAuth'));
            if (count($googleContacts > 0)) {
                foreach ($googleContacts as $googleContact) {
                    array_push($contactsSuggestions, $googleContact);
                }
            }

            return new JsonResponse($contactsSuggestions);
        }
    }
}
