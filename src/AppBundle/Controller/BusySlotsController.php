<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BusySlotsController extends Controller
{

    /**
     * @Route("/dispos/{start_time}/{end_time}")
     */
    public function findBusySlots($start_time, $end_time)
    {
        $BusySlotsService = $this->get('app.service.busyslots');
        $busy_slots = $BusySlotsService->retrieveBusySlots($start_time, $end_time);
        return new JsonResponse($busy_slots);
    }
}
