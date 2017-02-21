<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BusySlotsController extends Controller
{

    /**
     * @Route("/dispos/{start_time}/{end_time}/{id_user}")
     */
    public function findBusySlots($start_time, $end_time, $id_user)
    {
        $BusySlotsService = $this->get('app.service.busyslots');
        $busy_slots = $BusySlotsService->retrieveBusySlots($start_time, $end_time, $id_user);
        return new JsonResponse($busy_slots);
    }
}
