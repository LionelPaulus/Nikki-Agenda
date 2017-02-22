<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team_Members;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Team_member controller.
 *
 * @Route("team_members")
 */
class Team_MembersController extends Controller
{
    /**
     * Lists all team_Member entities.
     *
     * @Route("/", name="team_members_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $team_Members = $em->getRepository('AppBundle:Team_Members')->findAll();

        return $this->render('team_members/index.html.twig', array(
            'team_Members' => $team_Members,
        ));
    }

    /**
     * Creates a new team_Member entity.
     *
     * @Route("/new", name="team_members_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $team_Member = new Team_Members();
        $form = $this->createForm('AppBundle\Form\Team_MembersType', $team_Member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team_Member);
            $em->flush($team_Member);

            return $this->redirectToRoute('team_members_show', array('id' => $team_Member->getId()));
        }

        return $this->render('team_members/new.html.twig', array(
            'team_Member' => $team_Member,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a team_Member entity.
     *
     * @Route("/{id}", name="team_members_show")
     * @Method("GET")
     */
    public function showAction(Team_Members $team_Member)
    {
        $deleteForm = $this->createDeleteForm($team_Member);

        return $this->render('team_members/show.html.twig', array(
            'team_Member' => $team_Member,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing team_Member entity.
     *
     * @Route("/{id}/edit", name="team_members_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Team_Members $team_Member)
    {
        $deleteForm = $this->createDeleteForm($team_Member);
        $editForm = $this->createForm('AppBundle\Form\Team_MembersType', $team_Member);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_members_edit', array('id' => $team_Member->getId()));
        }

        return $this->render('team_members/edit.html.twig', array(
            'team_Member' => $team_Member,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a team_Member entity.
     *
     * @Route("/{id}", name="team_members_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Team_Members $team_Member)
    {
        $form = $this->createDeleteForm($team_Member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team_Member);
            $em->flush($team_Member);
        }

        return $this->redirectToRoute('team_members_index');
    }

    /**
     * Creates a form to delete a team_Member entity.
     *
     * @param Team_Members $team_Member The team_Member entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Team_Members $team_Member)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('team_members_delete', array('id' => $team_Member->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
