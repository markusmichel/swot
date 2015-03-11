<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Security\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        /** @var Translator $translator */
        $translator = $this->get('translator');

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('SwotNetworkBundle:User')->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($user === null) {
            $this->addFlash('notice', $translator->trans('user.profile.show.does_not_exist'));
            return $this->redirectToRoute('my_friends');
        }

        if(!$this->isGranted(UserVoter::SHOW, $user)) {
            $this->addFlash('notice', $translator->trans('user.profile.show.not_authed'));
            return $this->redirectToRoute('my_friends');
        }

        return $this->render('SwotNetworkBundle:Friend:show.html.twig', array(
            'user'  => $user,
            'breakUpForm' => $this->createRemoveFriendshipForm($user)->createView(),
        ));
    }

    public function removeFriendshipAction(Request $request, $id) {
        /** @var Translator $translator */
        $translator = $this->get('translator');

        /** @var User $user */
        $user = $this->getUser();

        /** @var User $friend */
        $friend = $this->getDoctrine()->getRepository('SwotNetworkBundle:User')->find($id);

        if($friend === null) {
            $this->addFlash('notice', $translator->trans('User does not exist'));
            return $this->redirectToRoute('my_friends');
        }

        $form = $this->createRemoveFriendshipForm($friend);
        $form->handleRequest($request);

        if(!$form->isValid() || !$user->isFriendOf($friend)) {
            return $this->redirectToRoute('my_friends');
        }

        $this->addFlash('notice', $translator->trans('user.friend.break_up.success', array('%username%' => $friend->getUsername())));
        return $this->redirectToRoute('my_friends');
    }

    /**
     * @param $user User
     * @return \Symfony\Component\Form\Form
     */
    private function createRemoveFriendshipForm($user) {
        $translator = $this->get('translator');
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('friend_remove_friendship', array('id' => $user->getId())))
            ->setMethod('POST')
            ->add('submit', 'submit', array(
                'label' => $translator->trans('Break with %username%', array(
                        "%username%" => $user->getUsername()
                    )
                )
            ))
            ->getForm();

        return $form;
    }

}
