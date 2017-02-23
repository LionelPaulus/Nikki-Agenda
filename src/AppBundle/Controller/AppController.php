<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\Team_Members;
use AppBundle\Form\TeamType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppController extends Controller
{
    /**
     * @Route("/app", name="app")
     */
    public function appAction(Request $request)
    {
        $session = $request->getSession();

        if (empty($session->get('userId'))) {
            // Check if user is logged, if not redirect to homepage
            return $this->redirectToRoute('homepage');
        } else {
            // User is logged

            // Get all teams where the user belongs to
            $user_teams = [];
            $events = [];
            $em = $this->getDoctrine()->getEntityManager();
            // Get user id
            $user = $em->getRepository('AppBundle:User')->findOneByEmail($session->get('userEmail'));
            // Get all the teams id where the user belongs to
            $teams = $em->getRepository('AppBundle:Team_Members')->findByUserId($user->getId());
            if ($teams) {
                // For each one, get details
                foreach ($teams as $team) {
                    $team_details = $em->getRepository('AppBundle:Team')->findOneById($team->getTeamId());
                    array_push($user_teams, array(
                        "id" => $team->getTeamId(),
                        "name" => $team_details->getName(),
                    ));

                    $api_events = $em->getRepository('AppBundle:Event')->findByTeamId($team->getTeamId());

                    $googleCalendarService = $this->get('app.service.google_calendar_api');
                    foreach ($api_events as $event) {
                        $event_details = $googleCalendarService->getEvent($event->getCreatorId(), $event->getGoogleCalendarId());
                        array_push($events, [
                            "title" => $event_details->summary,
                            "teamName" => $team_details->getName(),
                            "location" => $event_details->location,
                            "startDate" => date("d/m - G\hi", strtotime($event_details->start->dateTime)),
                            "endDate" => $event_details->end->dateTime,
                            "link" => $event_details->htmlLink,
                        ]);
                    }
                }
            }

            // Create a new team form
            $team = new Team();

            $form = $this->createFormBuilder($team)
                ->add('name', TextType::class)
                ->add('description', TextType::class, array('required' => false))
                ->add('members', TextareaType::class, array(
                    'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'members-input'
                        )
                    ))
                ->add('save', SubmitType::class, array('label' => 'Create'))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Get all invited members
                $members = $form->get('members')->getData();
                $members = json_decode($members);

                // Security checks
                if (count($members) < 1) {
                    throw new \Exception("Sorry but you can't create a team if you are alone :( Go make friends !");
                }
                if (count($members) == 1) {
                    if ($members[0] == $session->get('userEmail')) {
                        throw new \Exception("Nope, you can't create a team with only yourself, selfish !");
                    }
                }

                // Create the team
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush($team);
                $teamId = $team->getId();

                // Add the team creator to the team
                $team_members = new Team_Members();
                $team_members->setTeamId($teamId);
                $team_members->setUserId($session->get('userId'));
                $team_members->setRole(1);

                $em = $this->getDoctrine()->getManager();
                $em->persist($team_members);
                $em->flush();

                // For each invited members, add him to the team
                foreach ($members as $member) {
                    if ((filter_var($member, FILTER_VALIDATE_EMAIL))&&($member != $session->get('userEmail'))) {
                        // Search if the user is registered
                        $user = $em->getRepository('AppBundle:User')->findOneByEmail($member);

                        if ($user) {
                            // If yes, get his ID
                            $userId = $user->getId();
                        } else {
                            // If not, create it and get his ID
                            $UserController = $this->get('UserController');
                            $userId = $UserController->inviteUser($member);
                        }

                        // Add the user to the team
                        $team_members = new Team_Members();
                        $team_members->setTeamId($teamId);
                        $team_members->setUserId($userId);
                        $team_members->setRole(0); // Role 0 = not the creator

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($team_members);
                        $em->flush();
                    }
                }

                // Redirect to the team
                return $this->redirectToRoute('teamShow', array('id' => $teamId));
            }

            $formTwig = $form->createView();

            return $this->render('AppBundle:App:app.html.twig', array(
                'firstName' => $session->get('userFirstName'),
                'team' => $team,
                'form' => $form->createView(),
                'user_teams' => $user_teams,
                'events' => $events,
                'counter_events' => count($events)
            ));
        }
    }

    /**
     * @Route("/app/{id}", name="teamShow", requirements={"id": "\d+"})
     */
    public function showAction($id, Request $request)
    {
        $session = $request->getSession();

        if (empty($session->get('userGoogleAuth'))) {
            // Check if user is logged, if not redirect to homepage
            return $this->redirectToRoute('homepage');
        } else {
            // User is logged

            // Get team details
            $em = $this->getDoctrine()->getManager();
            $team_details = $em->getRepository('AppBundle:Team')->findOneById($id);
            $team = [];
            $team["name"] = $team_details->getName();
            $team["description"] = $team_details->getDescription();

            // Get team members
            $team["members"] = [];
            $team_members = $em->getRepository('AppBundle:Team_Members')->findByTeamId($id);
            foreach ($team_members as $member) {
                $member_details = $em->getRepository('AppBundle:User')->findOneById($member->getUserId());
                array_push($team["members"], array(
                    "firstName" => $member_details->getFirstName(),
                    "lastName" => $member_details->getLastName(),
                    "email" => $member_details->getEmail()
                ));
            }

            return $this->render('AppBundle:App:team.html.twig', array(
                'team' => $team,
            ));
        }
    }
}
