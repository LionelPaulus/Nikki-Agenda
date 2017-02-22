<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DisposTeamController extends Controller
{

    /**
     * @Route("/dispos-team/{start_time}/{end_time}/{id_team}/{duration}")
     */
    public function findDisposTeam($start_time, $end_time, $id_team, $duration)
    {
        $findDisposTeam = $this->get('app.service.disposteams');
        $dispos_teams = $findDisposTeam->retrieveDisposTeam($start_time, $end_time, $id_team, $duration);

        return new Response($dispos_teams);
    }
}
