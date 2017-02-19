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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AppController extends Controller
{
    /**
     * @Route("/app", name="app")
     */
    public function appAction(Request $request)
    {
        $session = $request->getSession();

        if (empty($session->get('userGoogleAuth'))) {
            // Check if user is logged, if not redirect to homepage
            return $this->redirectToRoute('homepage');
        } else {
            // User is logged

            // Get all teams where the user belongs to
            $user_teams = [];
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
                }
            }

            // Create a new team form
            $team = new Team();

            $form = $this->createFormBuilder($team)
                ->add('name', TextType::class)
                ->add('description', TextType::class, array('required' => false))
                ->add('members', TextType::class, array('mapped' => false, 'attr' => array('id' => 'members-input')))
                ->add('save', SubmitType::class, array('label' => 'Create'))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $members = $form->get('members')->getData();
                foreach ($members as $member) {
                    echo $member;
                }

                $team_members = new Team_Members();
                $team_members->setTeamId($teamId);
                $team_members->setUserId($userId);
                $team_members->setRole($role);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush($team);

                //  return $this->redirectToRoute('team_show', array('id' => $team->getId()));
            }
            $formTwig = $form->createView();

            return $this->render('AppBundle:App:app.html.twig', array(
                'firstName' => $session->get('userFirstName'),
                'team' => $team,
                'form' => $form->createView(),
                'user_teams' => $user_teams
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
                ));
            }

            return $this->render('AppBundle:App:team.html.twig', array(
                'team' => $team,
            ));
        }
    }
}
