<?php

namespace Swot\NetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{

    public function conversationsAction() {
        return $this->render('SwotNetworkBundle:Message:conversations.html.twig', array(

        ));
    }


    public function conversationAction($id) {
        return $this->render('SwotNetworkBundle:Message:conversation.html.twig', array(

        ));
    }


    public function writeAction() {

    }
}
