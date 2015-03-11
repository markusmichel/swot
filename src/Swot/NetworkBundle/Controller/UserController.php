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
    /**
     * Shows the current user's profile.
     * Renders the same template as FriendController::show but without break up form.
     * @see Swot\NetworkBundle\Controller\FriendController::showAction
     * @return Response
     */
    public function profileAction() {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('SwotNetworkBundle:Friend:show.html.twig', array(
            'user'  => $user,
        ));
    }


    public function settingsAction() {
        return $this->render('SwotNetworkBundle:User:settings.html.twig', array(

        ));
    }

}
