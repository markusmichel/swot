<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Swot\NetworkBundle\Entity\Conversation;
use Swot\NetworkBundle\Entity\ConversationRepository;
use Swot\NetworkBundle\Entity\Message;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Form\NewMessageToUserType;
use Swot\NetworkBundle\Form\NewMessageType;
use Swot\NetworkBundle\Security\MessageVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{

    /**
     * Lists all vonversations of the current user.
     * @return Response
     */
    public function conversationsAction() {
        /** @var ConversationRepository $repo */
        $repo = $this->getDoctrine()->getRepository('SwotNetworkBundle:Conversation');

        $conversations = $repo->findUsersConversations($this->getUser());

        return $this->render('SwotNetworkBundle:Message:conversations.html.twig', array(
            'conversations' => $conversations,
        ));
    }

    /**
     * Lists all messages in one specific conversation involving the current user.
     * @param $id ID of the user the current user has a conversation with.
     * @return Response
     */
    public function conversationAction(Request $request, $id) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Conversation $conversation */
        $conversation = $this->getDoctrine()->getRepository('SwotNetworkBundle:Conversation')->find($id);

        // Entity not found
        if($conversation === null) {
            // @todo: message string in translation file
            $this->addFlash('error', 'Conversation does not exist');
            return $this->redirectToRoute('conversations');
        }

        $messages = $this->getDoctrine()->getRepository('SwotNetworkBundle:Message')->findMessagesInConversation($conversation);

        /** @var User $partner */
        $partners = $conversation->getAllUsersBut($user);
        $partner = $partners->first();

        // User not involved in the requested conversation
        if(!$user->getConversations()->contains($conversation)) {
            // @todo: message string in translation file
            $this->addFlash('error', 'You are not allowed to see this conversation');
            return $this->redirectToRoute('conversations');
        }

        $deleteForms = array();
        /** @var Message $message */
        foreach($messages as $message) {
            $deleteForms[$message->getId()] = $this->createDeleteMessageForm($message)->createView();
        }

        $message = new Message();
        $message->setFrom($user);
        $message->setTo($partner);
        $message->setConversation($conversation);

        $messageForm = $this->createForm(new NewMessageToUserType(), $message);
        $messageForm->handleRequest($request);
        if($messageForm->isValid()) {
            $conversation->addMessage($message);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($message);
            $manager->persist($conversation);
            $manager->flush();

            return $this->redirectToRoute('conversation', array('id' => $conversation->getId()));
        }

        return $this->render('SwotNetworkBundle:Message:conversation.html.twig', array(
            'messages'  => $messages,
            'conversation' => $conversation,
            'partner'   => $partner,
            'deleteForms' => $deleteForms,
            'messageForm' => $messageForm->createView(),
        ));
    }

    /**
     * Starts a new conversation with the specified user.
     *
     * @param Request $request
     * @param $id ID of the user to start a new conversation with.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newConversationAction(Request $request, $id) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var User $partner */
        $partner = $this->getDoctrine()->getRepository('SwotNetworkBundle:User')->find($id);

        if($partner === null) {
            $this->addFlash('error', 'User does not exist');
            return $this->redirectToRoute('newsfeed');
        }

        /** @var Conversation $conversation */
        $conversation = $this->getDoctrine()->getRepository('SwotNetworkBundle:Conversation')->findConversationBetween($user, $partner);

        if($conversation === null) {
            $conversation = new Conversation();
            $user->addConversation($conversation);
            $partner->addConversation($conversation);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($conversation);
            $manager->persist($user);
            $manager->persist($partner);
            $manager->flush();
        }

        return $this->redirectToRoute('conversation', array('id' => $conversation->getId()));
    }

    /**
     * Displays a new message form.
     * The receiver is not known yet so the form displays a message input and a friend selector.
     *
     * @param Request $request
     * @return Response
     */
    public function newMessageAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $message = new Message();
        $message->setFrom($user);

        $form = $this->createForm('new_message', $message);
        $form->handleRequest($request);
        if($form->isValid() === true) {
            /** @var User $userTo */
            $userTo = $message->getTo();

            $conversation = $this->getDoctrine()->getRepository('SwotNetworkBundle:Conversation')->findConversationBetween($user, $userTo);
            if($conversation === null) {
                $conversation = new Conversation();
                $user->addConversation($conversation);
                $userTo->addConversation($conversation);
            }

            $conversation->addMessage($message);

            $message->setConversation($conversation);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->persist($userTo);
            $manager->persist($message);
            $manager->persist($conversation);

            $manager->flush();

            return $this->redirectToRoute('conversation', array('id' => $conversation->getId()));
        }

        return $this->render('SwotNetworkBundle:Message:new_message.html.twig', array(
            'newMessageForm' => $form->createView(),
        ));
    }


    public function deleteMessageAction(Request $request, $id) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Message $message */
        $message = $this->getDoctrine()->getRepository('SwotNetworkBundle:Message')->find($id);

        if(!$this->isGranted(MessageVoter::DELETE, $message)) {
            $this->addFlash('error', 'you may not remove this message');
            return $this->redirectToRoute('conversations');
        }

        $form = $this->createDeleteMessageForm($message);
        $form->handleRequest($request);
        if($form->isValid()) {
            /** @var Conversation $conversation */
            $conversation = $message->getConversation();
            $conversation->removeMessage($message);
            $message->setConversation(null);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($conversation);
            $manager->remove($message);
            $manager->flush();

            $this->addFlash('success', 'Message removed');
            return $this->redirectToRoute('conversation', array('id' => $conversation->getId()));
        }
    }

    private function createDeleteMessageForm(Message $message) {
        $form = $this->createFormBuilder($message)
            ->setAction($this->generateUrl('delete_message', array('id' => $message->getId())))
            ->setMethod('POST')
            ->add('button', 'submit', array(
                'label' => 'delete',
            ))
            ->getForm();

        return $form;
    }
}
