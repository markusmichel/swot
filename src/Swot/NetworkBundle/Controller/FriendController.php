<?php

namespace Swot\NetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FriendController extends Controller
{
    /**
     * Shows the logged in user's friends list.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myFriendsAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('SwotNetworkBundle:Friend:list.html.twig', array(
            'user'      => $user,
        ));
    }

    public function showAction($id)
    {
        return $this->render('SwotNetworkBundle:Friend:show.html.twig', array(
                // ...
            ));    }

}
