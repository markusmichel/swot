<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Security\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function profileAction() {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('SwotNetworkBundle:Friend:show.html.twig', array(
            'user'  => $user,
        ));
    }

}
