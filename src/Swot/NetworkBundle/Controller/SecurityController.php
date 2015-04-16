<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Form\UserType;
use Swot\NetworkBundle\Security\AccessType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        /** @var AuthenticationUtils $authenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // register form
        $userToRegister = new User();
        $registerForm = $this->createForm(new UserType(), $userToRegister);

        if($request->getMethod() === 'POST') {
            $registerForm->handleRequest($request);

            if($registerForm->isValid()) {
                /** @var PasswordEncoderInterface $encoder */
                $encoder = $this->get('security.password_encoder');
                $encodedPassword = $encoder->encodePassword($userToRegister, $userToRegister->getPassword());

                /**
                 * activate user by default unless mail confirmation is implemented
                 */
                $userToRegister->setActivated(true);
                $userToRegister->setAccessLevel(AccessType::ACCESS_TYPE_RESTRICTED);

                $userToRegister->setPassword($encodedPassword);

                $this->getDoctrine()->getManager()->persist($userToRegister);
                $this->getDoctrine()->getManager()->flush();

                // login registered user
                $token = new UsernamePasswordToken($userToRegister, null, 'main', $userToRegister->getRoles());
                $this->get('security.token_storage')->setToken($token);

                return $this->redirect($this->generateUrl('newsfeed'));
            }

        }

        return $this->render('SwotNetworkBundle:Security:login.html.twig', array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
            'register_form' => $registerForm->createView(),
        ));
    }

    public function loginCheckAction()
    {

    }



}
