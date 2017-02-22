<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Response;

class DisposTeamService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function retrieveDisposTeam($start_time, $end_time, $id_team)
    {
        return 'hello-team-service';
    }
}
