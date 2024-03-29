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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsfeedAction()
    {
        return $this->render('SwotNetworkBundle:Frontend:newsfeed.html.twig');
    }

    /**
     * @param $since
     * @return JsonResponse
     */
    public function newsfeedSinceAction($since) {
        $sinceDate = new \DateTime();
        $sinceDate->setTimestamp(intval($since));

        $user = $this->getUser();
        $newsfeed = $this->getDoctrine()->getRepository('SwotNetworkBundle:ThingStatusUpdate')->findUserNewsfeedSince($user, $sinceDate);

        $view = $this->renderView("SwotNetworkBundle:Frontend:_newsfeed_items.html.twig", array(
            "newsfeed" => $newsfeed
        ));
        return new Response($view);
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
            'form' => $form->createView()
        ));
    }

    /**
     * Internal action without route.
     *
     * Shows random public things
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showRandomPublicThingsAction() {
        /** @var User $user */
        $user = $this->getUser();

        $randomPublicThingsCount =$this->container->getParameter('random.publicthings.count');
        $thingRepo = $this->getDoctrine()->getRepository('SwotNetworkBundle:Thing');
        $publicThings = $thingRepo->findRandomPublicThings($user, $randomPublicThingsCount);
        return $this->render("SwotNetworkBundle:Frontend:_random_publicthings.html.twig", array(
            "publicthings" => $publicThings,
        ));
    }

    public function deleteThingAction(Request $request, $id) {
        return new Response("delete " . $id);
    }

}
