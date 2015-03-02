<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class SecurityController extends Controller
{
    public function loginAction()
    {
        $user = new User();
        $user->setActivated(true);
        $user->setBirthdate(new \DateTime());
        $user->setFirstName("Markus");
        $user->setLastName("Michel");
        $user->setGender("m");
        $user->setProfileImage("");
        $user->setRegisteredDate(new \DateTime());
        $user->setUsername("markus");

        /** @var PasswordEncoderInterface $encoder */
        $encoder = $this->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, "markus"));

//        $this->getDoctrine()->getManager()->persist($user);
//        $this->getDoctrine()->getManager()->flush();

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('SwotNetworkBundle:Security:login.html.twig', array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function loginCheckAction()
    {

    }

}
