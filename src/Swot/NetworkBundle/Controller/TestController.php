<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function generateTestDataAction() {
        $this->generateTestThings();
        $this->generateTestFriends();
        $this->generateFriendWithThingAndLendToUser();

        return new Response("Test data generated");
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
        $thing->setOwnership($ownership);
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
        $user->setActivated(true);
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
        $thing->setNetworkAccessToken("token123");
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
        $thing->setOwnership($ownership);
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
