<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Swot\NetworkBundle\Entity\Conversation;
use Swot\NetworkBundle\Entity\ConversationRepository;
use Swot\NetworkBundle\Entity\Message;
use Swot\NetworkBundle\Entity\User;
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

        $deleteForms = array();
        foreach($conversation->getMessages() as $message) {
            $deleteForms[$message->getId()] = $this->createDeleteMessageForm($message)->createView();
        }

        return $this->render('SwotNetworkBundle:Message:conversation.html.twig', array(
            'messages'  => $conversation->getMessages(),
            'partner'   => $partner,
            'deleteForms' => $deleteForms,
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
            echo "message text = " . $message->getText();
            echo "<br>";
            die("conversation id = " . $conversation->getId());
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
