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
        $user = $this->getUser();
        $newsfeed = $this->getDoctrine()->getRepository('SwotNetworkBundle:ThingStatusUpdate')->findUserNewsfeed($user);

        return $this->render('SwotNetworkBundle:Frontend:newsfeed.html.twig', array(
            'newsfeed' => $newsfeed
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
        //return $this->render('SwotNetworkBundle:Frontend:myThings.html.twig');

        //@TODO: correct?!
        $data = array();
        $form = $this->createFormBuilder($data)
            ->add('register','file')
            ->getForm();
        return $this->render('SwotNetworkBundle:Frontend:myThings.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteThingAction(Request $request, $id) {
        return new Response("delete " . $id);
    }

}
