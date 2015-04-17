<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * Returns all Users who are activated and friends of the passed user.
     * The passed user will be excluded from the list.
     * @param User $user
     * @return array
     */
    public function findFriendsOf(User $user) {
        $query = $this->getEntityManager()->createQuery(
            'SELECT u
             FROM SwotNetworkBundle:Friendship f
             JOIN SwotNetworkBundle:User u
             WHERE (
                f.userWho = :user
                OR f.userWith = :user
             )
             AND u != :user
             AND u.activated = TRUE
             '
        )
            ->setParameter("user", $user)
        ;

        $result = $query->getResult();

        return $result;
    }

    public function findRandomStrangers(User $user, $count) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $friends = $qb->select('t')
                      ->from('SwotNetworkBundle:User', 't')
                      ->join('t.friendships', 'f')
                      ->where('f.userWith = :user')
                      ->orWhere('f.userWho = :user')
                      ->andWhere('t != :user')
                      ->setParameter("user", $user)
                      ->getQuery()
                      ->getResult();

        $friendIds = array();
        foreach($friends as $friend) {
            $friendIds[] = $friend->getId();
        }

        $qb->resetDQLParts()
            ->select('u')
            ->from('SwotNetworkBundle:User', 'u');

        if(count($friendIds) > 0) {
            $qb->where($qb->expr()->notIn('u.id', $friendIds));
        }

        $qb->andWhere('u != :user')
            ->andWhere('u.activated = TRUE')
            ->setParameter("user", $user);

        $result = $qb->getQuery()->getResult();
        shuffle($result);
        return array_slice($result, 0, $count);
    }
}
