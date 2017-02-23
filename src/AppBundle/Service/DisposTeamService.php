<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DisposTeamService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function retrieveDisposTeam($start_time, $end_time, $id_team, $duration)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        // Get users from team
        $users = $em->getRepository('AppBundle:Team_Members')->findByTeamId($id_team);

        $findBusySlots = $this->container->get('app.service.busyslots');

        $team_busy_slots = array();

        foreach ($users as $user) {
            $id_user = $user->getUserId();
            // dump($id_user);
            $user_busy = $findBusySlots->retrieveBusySlots($start_time, $end_time, $id_user);
            // dump($user_busy);
            foreach ($user_busy as $busy) {
                array_push($team_busy_slots, $busy);
            }
        }

        dump($team_busy_slots);
        die();
        $free_time_slots = array();

        $count = count($team_busy_slots)-1;
        $i = 0;

        foreach ($team_busy_slots as $event) {
            if ($i < $count) {
                  $free_time = $team_busy_slots[$i+1]['start'] - $event['end'];
                  $free_time_slots[] = array(
                      'start' => date("F j, Y, g:i a", $event['end']),
                      'end' => date("F j, Y, g:i a", $team_busy_slots[$i+1]['start']),
                      'minutes' => $free_time / 60
                  );
                  $i++;
            }
        }

        dump($free_time_slots);
        return new JsonResponse($free_time_slots);
    }
}
