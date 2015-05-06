<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ConversationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConversationRepository extends EntityRepository
{
    /**
     * Return a users' conversations ordered by last updated date.
     * @param User $user
     * @return Conversation
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUsersConversations(User $user) {
        $query = $this->getEntityManager()->createQuery('
            SELECT c
            FROM SwotNetworkBundle:User u
            JOIN SwotNetworkBundle:Conversation c
            WHERE u = :user
            AND c MEMBER OF u.conversations
            ORDER BY c.updated DESC
        ')
            ->setParameter('user', $user);

        /** @var Conversation $conversation */
        $conversation = $query->getResult();
        return $conversation;
    }

    /**
     * Returns a Conversation between two users if it already exists or null.
     * @param User $user1
     * @param User $user2
     * @return Conversation
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConversationBetween(User $user1, User $user2) {
        $query = $this->getEntityManager()->createQuery("
            SELECT c FROM SwotNetworkBundle:Conversation c
            WHERE
                :user1 MEMBER OF c.involvedUsers
            AND :user2 MEMBER OF c.involvedUsers
        ")
        ->setParameter("user1", $user1)
        ->setParameter("user2", $user2)
        ;

        /** @var Conversation $conversation */
        $conversation = $query->getOneOrNullResult();

        return $conversation;
    }
}
