<?php

namespace Swot\NetworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

        $randomStrangersCount = 10;
        $strangers = $this->getRandomStrangers($user, $randomStrangersCount);

        return $this->render('SwotNetworkBundle:Frontend:myFriends.html.twig', array(
            'user'      => $user,
            'strangers' => $strangers
        ));
    }

    /**
     * Shows any users profile if the current user has permission to see it.
     * @ParamConverter("user", class="SwotNetworkBundle:User")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function showAction(User $user)
    {
        /** @var Translator $translator */
        $translator = $this->get('translator');

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($user === null) {
            $this->addFlash('notice', $translator->trans('user.profile.show.does_not_exist'));
            return $this->redirectToRoute('my_friends');
        }

        // @todo: what to do with private profiles? show restricted profile or completely restrict access?
//        if(!$this->isGranted(UserVoter::SHOW, $user)) {
//            $this->addFlash('notice', $translator->trans('user.profile.show.not_authed'));
//            return $this->redirectToRoute('my_friends');
//        }

        $conversation = $this->getDoctrine()->getRepository('SwotNetworkBundle:Conversation')->findConversationBetween($this->getUser(), $user);
        $sendMessageLink = $conversation !== null ?
            $this->generateUrl('conversation', array('id' => $conversation->getId())) :
            $this->generateUrl('new_message');

        return $this->render('SwotNetworkBundle:Friend:show.html.twig', array(
            'user'              => $user,
            'breakUpForm'       => $this->createRemoveFriendshipForm($user)->createView(),
            'sendInviteForm'    => $this->createSendInviteForm($user)->createView(),
            'sendMessageLink'   => $sendMessageLink,
        ));
    }

    /**
     * Removes a friendship relation between the current user and another user.
     *
     * This is the result of a form action and only callable by POST|DELETE.
     * This action is CSRF protected.
     *
     * @param Request $request
     * @ParamConverter("friend", class="SwotNetworkBundle:User")
     * @param User $friend
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFriendshipAction(Request $request, User $friend) {
        /** @var Translator $translator */
        $translator = $this->get('translator');

        /** @var User $user */
        $user = $this->getUser();

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
            $this->addFlash('notice', $translator->trans('user.friend.friendship.does_not_exist', array('%username%' => $friend->getUsername())));
            return $this->redirectToRoute('friend_show', array('id' => $friend->getId()));
        }

        $this->container->get("swot.manager.friendship")->remove($friendship);

        $this->addFlash('notice', $translator->trans('user.friend.break_up.success', array('%username%' => $friend->getUsername())));
        return $this->redirectToRoute('friend_show', array('id' => $friend->getId()));
    }

    /**
     * Send a friendship invite from the current user to the requested user.
     * Redirects to the friend's profile if they are already friends.
     *
     * Currently makes them immediately to friends without invitiation.
     * @todo: Send invite which must be accepted by the other user
     *
     * @param Request $request
     * @ParamConverter("friend", class="SwotNetworkBundle:User")
     * @param User $friend
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function sendInviteAction(Request $request, User $friend) {
        /** @var Translator $translator */
        $translator = $this->get('translator');

        /** @var User $user */
        $user = $this->getUser();

        if($friend === null) {
            $this->addFlash('notice', $translator->trans('user.does_not_exist'));
            return $this->redirectToRoute('my_friends');
        }

        // User cannot be a friend of himself
        // Should never happen
        if($friend === $user) {
            $this->addFlash('notice', $translator->trans('user.does_not_exist'));
            return $this->redirectToRoute('my_friends');
        }

        // Cancel if the users are already friends
        if($this->isGranted('friend', $friend)) {
            $this->addFlash('notice', $translator->trans('user.friend.friendship.already_friends'));
            return $this->redirectToRoute('friend_show', array('id' => $friend->getId()));
        }

        // Make them friends
        $this->container->get("swot.manager.friendship")->create($user, $friend);

        $this->addFlash('notice', $translator->trans('user.friend.friendship.invite.success', array('%username%' => $friend->getUsername())));
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

    /**
     * Create a form to send a friendship invite.
     * @param $user User
     * @return \Symfony\Component\Form\Form
     */
    public function createSendInviteForm($user) {
        $translator = $this->get('translator');
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('friend_invite', array('id' => $user->getId())))
            ->setMethod('POST')
            ->add('submit', 'submit', array(
                'label' => $translator->trans('user.friend.friendship.invite.label', array(
                        "%username%" => $user->getUsername()
                    )
                )
            ))
            ->getForm();

        return $form;
    }

    /**
     * @param User $user
     * @param $randomStrangersCount
     * @return random people who are not befriended with user
     */
    private function getRandomStrangers(User $user, $randomStrangersCount)
    {
        $userRepo = $this->getDoctrine()->getRepository('SwotNetworkBundle:User');
        $strangers = $userRepo->findRandomStrangers($user, $randomStrangersCount);
        return $strangers;
    }
}
