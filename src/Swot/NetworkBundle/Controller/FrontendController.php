<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class FrontendController extends Controller
{
    /**
     * Index page when logged in.
     * Shows logged in user's newsfeed.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsfeedAction()
    {
        return $this->render('SwotNetworkBundle:Frontend:newsfeed.html.twig', array(
            // ...
        ));
    }

    /**
     * Shows the logged in user's things.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myThingsAction(Request $request) {
//        $this->generateTestThings();
//        $this->generateFriendWithThingAndLendToUser();

        return $this->render('SwotNetworkBundle:Frontend:myThings.html.twig');
    }

    public function deleteThingAction(Request $request, $id) {
        return new Response("delete " . $id);
    }

}
