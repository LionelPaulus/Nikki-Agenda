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

    public function retrieveDisposTeam($start_time, $end_time, $id_team, $duration)
    {
        $findBusySlots = $this->container->get('app.service.busyslots');
        $busy_slots = $findBusySlots->retrieveBusySlots($start_time, $end_time, $id_team, $duration);
        // dump($busy_slots);

        $free_time_slots = array();
        $count = count($busy_slots)-1;
        $i = 0;
        foreach ($busy_slots as $event) {
            if ($i < $count) {
                  $free_time = $busy_slots[$i+1]['start'] - $event['end'];
                  $free_time_slots[] = array(
                      'start' => date("F j, Y, g:i a", $event['end']),
                      'end' => date("F j, Y, g:i a", $busy_slots[$i+1]['start']),
                      'minutes' => $free_time / 60
                  );
                  $i++;
            }
        }

        dump($free_time_slots);
        die();
        return 'hello-team-service';
    }
}
