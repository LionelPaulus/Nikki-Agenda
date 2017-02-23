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
            $user_busy = $findBusySlots->retrieveBusySlots($start_time, $end_time, $id_user);
            foreach ($user_busy as $busy) {
                array_push($team_busy_slots, $busy);
            }
        }

        $start_time = new \DateTime($start_time);
        $end_time = new \DateTime($end_time);

        $interval = $start_time->diff($end_time);
        $interval = $interval->days;

        $morning_clamping = array();

        for ($i=0; $i < $interval + 1; $i++) {
            $day = '+'.$i.' day';
            $clamped_day = new \DateTime($start_time->format('Y-m-d').$day);
            array_push($team_busy_slots,
            [
              "start" => date_timestamp_get(date_time_set($clamped_day, 00, 00, 00)),
              "end" => date_timestamp_get(date_time_set($clamped_day, 10, 00, 00))
            ]);
        }

        usort($team_busy_slots, function ($a, $b) {
            $ad = $a["start"];
            $bd = $b["start"];
            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? -1 : 1;
        });

        $findBusySlots = $this->container->get('app.service.superpositionkiller');
        $team_busy = $findBusySlots->superpositionKiller($team_busy_slots);

        $free_time_slots = array();
        $events = $team_busy;
        $count = count($events)-1;
        $i = 0;
        foreach ($events as $event) {
            if ($i < $count) {
                $free_time = $events[$i+1]['start'] - $event['end'];
                $free_time_slots[] = array(
                    'start' => $event['end'],
                    'end' => $events[$i+1]['start'],
                    'minutes' => $free_time / 60
                );
                $i++;
            }
        }

        $team_dispos = array();

        foreach ($free_time_slots as $range) {
            if ($range["minutes"] >= $duration) {
                array_push($team_dispos,
                    [
                      "start" => $range["start"],
                      "end" => $range["start"]+$duration
                    ]
                );
            }
        }

        // foreach ($team_dispos as $dispo) {
        //     dump(date('c', $dispo["start"]));
        //     dump(date('c', $dispo["end"]));
        // }

        return new JsonResponse($team_dispos);
    }
}
