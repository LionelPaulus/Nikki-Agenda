<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
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
        if (isset($_POST)) {
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
            "eventTitle",
            "fromDate",
            "toDate"
        ];
        if ((empty($_POST[$data]))||(is_nan($_POST[$data]))) {
            $this->response->code = "404";
            $this->response->message = "Missing ".$data." value or NAN";
            return new JsonResponse($this->response);
        }

        try {
            // Create the event
            $event = new Event();
            $event->setTeamId($_POST["teamId"]);
            $event->setCreatorId($request->getSession()->get('userId'));
            $event->setGoogleCalendarId("999");

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
