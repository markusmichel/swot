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
     * Shows the logged in user's friends list.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myFriendsAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        // generate test friend with friendship relation
//        $this->generateTestFriends();

        return $this->render('SwotNetworkBundle:Frontend:myFriends.html.twig', array(
            'user'      => $user,
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

    private function generateTestFriends() {
        /** @var User $user */
        $user = $this->getUser();

        $friend = $this->generateTestUser();

        $friendship = new Friendship();
        $friendship->setUserWho($user);
        $friendship->setUserWith($friend);

        $user->addFriendship($friendship);
        $friend->addFriendship($friendship);

        $this->getDoctrine()->getManager()->persist($friendship);
        $this->getDoctrine()->getManager()->persist($friend);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
    }

    private function generateTestThings() {
        /** @var User $user */
        $user = $this->getUser();

        $thing = $this->generateTestThing();

        $ownership = new Ownership();
        $ownership->setOwner($user);
        $ownership->setThing($thing);
        $thing->setOwner($ownership);
        $user->addOwnership($ownership);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($thing);
        $this->getDoctrine()->getManager()->flush();

    }

    private function generateTestUser() {
        /** @var PasswordEncoderInterface $encoder */
        $encoder = $this->get('security.password_encoder');

        $username = uniqid();

        $user = new User();
        $user->setUsername($username);
        $user->setFirstName("Max");
        $user->setLastName("Mustermann");
        $user->setBirthdate(new \DateTime());
        $user->setGender("m");
        $user->setPassword($encoder->encodePassword($user, "testuser"));

        return $user;
    }

    private function generateTestThing()
    {
        $thing = new Thing();
        $thing->setAccessToken("token123");
        $thing->setName(uniqid());
        return $thing;
    }

    private function generateFriendWithThingAndLendToUser()
    {
        $thing = $this->generateTestThing();
        $friend = $this->generateTestUser();
        $ownership = new Ownership();
        $ownership->setOwner($friend);
        $ownership->setThing($thing);
        $thing->setOwner($ownership);
        $friend->addOwnership($ownership);

        $rental = new Rental();
        $rental->setThing($thing);
        $rental->setUserFrom($friend);
        $rental->setUserTo($this->getUser());

        $this->getDoctrine()->getManager()->persist($friend);
        $this->getDoctrine()->getManager()->persist($thing);
        $this->getDoctrine()->getManager()->persist($rental);
        $this->getDoctrine()->getManager()->flush();
    }

}
