<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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
            // Get accessToken
            $accessToken = $client->getAccessToken();
            // Set accessToken
            $client->setAccessToken($accessToken);

            // Start Google_Service_Oauth2 for userinfos
            $google_oauth = new \Google_Service_Oauth2($client->getGoogleClient());
            $userinfos = $google_oauth->userinfo->get();

            // Create or update the user in DB using the UserController
            $UserController = $this->get('UserController');
            $UserController->userLogin(
                $accessToken['access_token'],
                $userinfos->givenName,
                $userinfos->familyName,
                $userinfos->picture,
                $userinfos->email
            );

            // Create PHP session and set userinfos
            $session = $request->getSession();
            $session->start();
            $session->set('code', $code);
            $session->set('userGoogleAuth', $accessToken);
            $session->set('userFirstName', $userinfos->givenName);
            $session->set('userLastName', $userinfos->familyName);
            $session->set('userPicture', $userinfos->picture);
            $session->set('userEmail', $userinfos->email);

            // Redirect to app
            return $this->redirectToRoute('app');
        } else {
            throw new \Exception("No code sent");
        }
    }
}
