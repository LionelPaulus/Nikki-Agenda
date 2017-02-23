<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccessTokenController extends Controller
{

    /**
     * @Route("/access/{user_id}")
     */
    public function exchangeAuth($user_id)
    {
        $AccessTokenService = $this->get('app.service.accesstoken');
        $accessToken = $AccessTokenService->getAccessToken($user_id);

        return $accessToken;
    }
}
