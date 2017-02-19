<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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
            
            return $this->render('AppBundle:App:app.html.twig', array(
                'firstName' => $session->get('userFirstName'),
            ));
        }
    }
}
