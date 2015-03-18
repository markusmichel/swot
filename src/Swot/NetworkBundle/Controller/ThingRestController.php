<?php

namespace Swot\NetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThingRestController extends Controller
{

    public function newMessageAction(Request $request) {
        return new Response("message sent");
    }

}
