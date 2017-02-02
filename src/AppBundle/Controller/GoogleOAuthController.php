<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class GoogleOAuthController extends Controller
{

    private $accessScope = [
      \Google_Service_Calendar::CALENDAR
    ];
    /**
     * @Route("/oauth/google/auth")
     */
    public function getAuthenticationCodeAction()
    {
        $client = $this->container->get('happyr.google.api.client');

        // Determine the level of access your application needs
        $client->getGoogleClient()->setScopes($this->accessScope);

        // Send the user to complete their part of the OAuth
        return $this->redirect($client->createAuthUrl());
    }

    /**
     * @Route("/oauth/google/redirect")
     */
    public function getAccessCodeRedirectAction(Request $request)
    {
        if ($request->query->get('code')) {
            $code = $request->query->get('code');

            $client = $this->container->get('happyr.google.api.client');
            $client->getGoogleClient()->setScopes($this->accessScope);
            $client->authenticate($code);

            $accessToken = $client->getGoogleClient()->getAccessToken();

            // TODO - Store the token, etc...
        } else {
            $error = $request->query->get('error');
            // TODO - Handle the error
        }
    }
}
