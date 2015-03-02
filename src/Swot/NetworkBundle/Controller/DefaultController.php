<?php

namespace Swot\NetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class DefaultController extends Controller
{
    public function indexAction()
    {
        /** @var AuthorizationChecker $checker */
        $checker = $this->get('security.authorization_checker');

        $user = $this->getUser();
        var_dump($user);
        die();

        if(!$checker->isGranted('ROLE_USER'))
            throw $this->createAccessDeniedException("Nur für registrierte Nutzer sichtbar");

        return $this->render('SwotNetworkBundle:Default:index.html.twig', array('name' => "test"));
    }

    public function fooAction() {
        return new Response("FOO");
    }
}
