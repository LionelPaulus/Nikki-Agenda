<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\Response;

class BusySlotsService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function retrieveBusySlots($start_time, $end_time, $id_user)
    {

        // Set google client
        $client = new \Google_Client();

        $em = $this->container->get('doctrine')->getEntityManager();
        // Get user id
        $user = $em->getRepository('AppBundle:User')->findOneById($id_user);

        $user_auth = $user->getGoogleAuth();

        // $accessToken = $this->container->get('session')->get('userGoogleAuth');
        $client->setAccessToken($user_auth);

        // Format datetime so it is usable by Google Freebusy api
        $start_time = new \DateTime($start_time, new \DateTimeZone('Europe/Berlin'));
        $end_time = new \DateTime($end_time, new \DateTimeZone('Europe/Berlin'));

        // $interval = $start_time->diff($end_time);
        // $interval = $interval->days;
        //
        // $morning_clamping = array();
        // $night_clamping = array();
        //
        // for ($i=0; $i < $interval + 1; $i++){
        //   $day = '+'.$i.' day';
        //   $clamped_day = new \DateTime($start_time->format('Y-m-d').$day);
        //   $morning_clamping[$i]["start"] = date_timestamp_get(date_time_set($clamped_day, 00, 00, 00));
        //   $morning_clamping[$i]["end"] = date_timestamp_get(date_time_set($clamped_day, 10, 00, 00));
        // }

        // dump($morning_clamping);
        // die();

        // for ($i=0; $i < $interval + 1; $i++){
        //   $day = '+'.$i.' day';
        //   $clamped_day = new \DateTime($start_time->format('Y-m-d').$day);
        //   $night_clamping[$i]["start"] = date_timestamp_get(date_time_set($clamped_day, 20, 00, 00));
        //   $night_clamping[$i]["end"] = date_timestamp_get(date_time_set($clamped_day, 22, 00, 00));
        // }

        // dump($morning_clamping);
        // die();
        $start_time = date('c', strtotime($start_time->format('Y-m-d H:i:sP')));
        $end_time = date('c', strtotime($end_time->format('Y-m-d H:i:sP')));

        // Retrieve calendars from user
        $calendar = new \Google_Service_Calendar($client);
        // $list = $calendar->calendarList->listCalendarList();

        // Create freebusy request
        $freebusy = new \Google_Service_Calendar_FreeBusyRequest();
        $freebusy->setTimeMin($start_time);
        $freebusy->setTimeMax($end_time);
        $freebusy->setTimeZone('Europe/Berlin');
        $item = new \Google_Service_Calendar_FreeBusyRequestItem();
        $item->setId('primary');
        $freebusy->setItems(array($item));
        $busy_slots = $calendar->freebusy->query($freebusy);
        // Fill events array with busy slots retrieved from user calendar
        $events = array();
        $i = 0;

        foreach ($busy_slots["calendars"]["primary"]["modelData"]["busy"] as $busy) {
            $events[$i] = $busy;
            $i ++ ;
        }

        $i = 0;

        // Convert events time format to timestamp
        foreach ($events as $event) {
            $events[$i]["start"] = strtotime($event["start"]);
            $events[$i]["end"] = strtotime($event["end"]);
            $i ++;
        }

        return $events;
    }
}
