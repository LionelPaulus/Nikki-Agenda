<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccessTokenService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getAccessToken($user_id)
    {
        $client = new \Google_Client();
        $em = $this->container->get('doctrine')->getEntityManager();
        // Get user id
        $user = $em->getRepository('AppBundle:User')->findOneById($user_id);

        $user_auth = $user->getGoogleAuth();
        $client->setAccessToken($user_auth);
        $accessToken = $client->fetchAccessTokenWithAuthCode();

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
            return json_encode($client->fetchAccessTokenWithAuthCode($user_auth));
        }

        return $accessToken;
    }
}
