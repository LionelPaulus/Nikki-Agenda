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

            $accessToken = $client->getAccessToken();
            // TODO: Store accessToken

            $client->setAccessToken($accessToken);

            $google_oauth = new \Google_Service_Oauth2($client->getGoogleClient());
            $userinfo = $google_oauth->userinfo->get();


            $UserController = $this->get('UserController');
            $UserController->userLogin(
                $accessToken['access_token'],
                $userinfo->givenName,
                $userinfo->familyName,
                $userinfo->picture,
                $userinfo->email
            );

            $session = $request->getSession();
            $session->start();

            $session->set('userGoogleAuth', $accessToken['access_token']);
            $session->set('userFirstName', $userinfo->givenName);
            $session->set('userLastName', $userinfo->familyName);
            $session->set('userPicture', $userinfo->picture);
            $session->set('userEmail', $userinfo->email);
            echo '<pre>';
            var_dump($session->get('userEmail'));
            echo '</pre>';

            die();

            $calendar = new \Google_Service_Calendar($client->getGoogleClient()); // Works
        } else {
            throw new \Exception("No code sent");
        }
    }
}
