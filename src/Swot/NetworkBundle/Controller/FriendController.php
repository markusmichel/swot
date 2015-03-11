<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Friendship;
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

    /**
     * Shows any users profile if the current user has permission to see it.
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
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

    /**
     * Removes a friendship relation between the current user and another user.
     *
     * This is the result of a form action and only callable by POST|DELETE.
     * This action is CSRF protected.
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
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

        /** @var Friendship $friendship */
        $friendship = $this->getDoctrine()->getRepository('SwotNetworkBundle:Friendship')->findFriendshipBetween($user, $friend);

        // Friendship between users does not exist.
        // Should never happen as the form is CSRF protected
        // and only shows up if the two users are friends.
        if($friendship === null) {
            $this->addFlash('notice', $translator->trans('user.friend.friendship.does_not_exist', array('username' => $friend->getUsername())));
            return $this->redirectToRoute('friend_show', array('id' => $friend->getId()));
        }

        $manager = $this->getDoctrine()->getManager();
        $user->removeFriendship($friendship);
        $friend->removeFriendship($friendship);
        $friendship->setUserWho(null);
        $friendship->setUserWith(null);

        $manager->persist($user);
        $manager->persist($friend);
        $manager->remove($friendship);
        $manager->flush();

        $this->addFlash('notice', $translator->trans('user.friend.break_up.success', array('%username%' => $friend->getUsername())));
        return $this->redirectToRoute('friend_show', array('id' => $friend->getId()));
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
                'label' => $translator->trans('user.friend.break_up.label', array(
                        "%username%" => $user->getUsername()
                    )
                )
            ))
            ->getForm();

        return $form;
    }

}
