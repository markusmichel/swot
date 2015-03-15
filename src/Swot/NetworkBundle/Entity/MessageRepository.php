<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    /**
     * Finds all Messages in a conversation ordered by sent DESC.
     *
     * @param Conversation $conversation
     * @return array
     */
    public function findMessagesInConversation(Conversation $conversation) {
        $query = $this->getEntityManager()->createQuery('
            SELECT m
            FROM SwotNetworkBundle:Message m
            JOIN m.conversation c
            WHERE c = :conversation
            ORDER BY m.sent ASC
        ')->setParameter('conversation', $conversation);

        return $query->getResult();
    }
}
