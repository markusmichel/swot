<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Swot\NetworkBundle\Entity\Conversation;
use Swot\NetworkBundle\Entity\ConversationRepository;
use Swot\NetworkBundle\Entity\Message;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Form\NewMessageType;
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
     * @param $id
     * @return Response
     */
    public function conversationAction($id) {
        /** @var User $user */
        $user = $this->getUser();

        $partner = $this->getDoctrine()->getRepository('SwotNetworkBundle:User')->find($id);

        // partner does not exist
        if($partner === null) {

        }

        // user is a friend of partner
        if(!$this->isGranted('friend', $partner)) {

        }

        /** @var Conversation $conversation */
        $conversation = $this->getDoctrine()->getRepository('SwotNetworkBundle:Conversation')->findConversationBetween($user, $partner);

        // Entity not found
        if($conversation === null) {

        }

        // User not involved in the requested conversation
        if(!$user->getConversations()->contains($conversation)) {

        }

        // Conversation has no messages
        if($conversation->getMessages()->isEmpty()) {

        }

        return $this->render('SwotNetworkBundle:Message:conversation.html.twig', array(
            'messages'  => $conversation->getMessages(),
            'partner'   => $partner,
        ));
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
            if($conversation === null) $conversation = new Conversation();

            $conversation->addMessage($message);

            $user->addConversation($conversation);
            $userTo->addConversation($conversation);

            $message->setConversation($conversation);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->persist($userTo);
            $manager->persist($message);
            $manager->persist($conversation);

            $manager->flush();
        }

        return $this->render('SwotNetworkBundle:Message:new_message.html.twig', array(
            'newMessageForm' => $form->createView(),
        ));
    }
}
