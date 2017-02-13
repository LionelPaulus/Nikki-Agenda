<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class GoogleOAuthController extends Controller
{

    private $accessScope = [
      \Google_Service_Calendar::CALENDAR,
      \Google_Service_People::CONTACTS_READONLY
    ];
    /**
     * @Route("/oauth/google/auth")
     */
    public function getAuthenticationCodeAction()
    {
        $client = $this->container->get('happyr.google.api.client');

        // Determine the level of access your application needs
        $client->getGoogleClient()->setScopes($this->accessScope);

        // Request access to basic informations
        $client->getGoogleClient()->addScope(array(
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ));

        // Request access to offline access
        $client->getGoogleClient()->setAccessType('offline');

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

            $accessToken = $client->getAccessToken();
            // TODO: Store accessToken

            $client->setAccessToken($accessToken);

            $google_oauth = new \Google_Service_Oauth2($client->getGoogleClient());
            $userinfo = $google_oauth->userinfo->get();

            echo '<pre>';
            var_dump($userinfo);
            echo '</pre>';
            die();

            $calendar = new \Google_Service_Calendar($client->getGoogleClient()); // Works
        } else {
            throw new \Exception("No code sent");
        }
    }
}
