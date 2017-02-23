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
        if (empty($request->getSession()->get('userId'))) {
            // Check if user is logged, if not redirect to googleAuth
            return $this->redirectToRoute('googleAuth');
        }

        return $this->redirectToRoute('app');
    }
}
