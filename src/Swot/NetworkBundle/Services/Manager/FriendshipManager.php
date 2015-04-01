<?php

namespace Swot\NetworkBundle\Services\Manager;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\FormMapperBundle\Entity\Action;
use Swot\FormMapperBundle\Entity\Parameter\Parameter;
use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingStatusUpdate;
use Swot\NetworkBundle\Entity\User;

/**
 *
 *
 * Class FriendshipManager
 * @package Swot\NetworkBundle\Services\Manager
 *
 * @DI\Service("swot.manager.friendship")
 */
class FriendshipManager {

    /** @var EntityManager */
    public $em;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function remove(Friendship $friendship) {
        $user = $friendship->getUserWho();
        $friend = $friendship->getUserWith();

        $user->removeFriendship($friendship);
        $friend->removeFriendship($friendship);

        $friendship->setUserWho(null);
        $friendship->setUserWith(null);

        $this->em->persist($user);
        $this->em->persist($friend);
        $this->em->remove($friendship);
        $this->em->flush();
    }

    public function create(User $user, User $friend) {
        $friendship = new Friendship();
        $friendship->setUserWho($friend);
        $friendship->setUserWith($user);
        $user->addFriendship($friendship);
        $friend->addFriendship($friendship);

        $this->em->persist($friendship);
        $this->em->persist($user);
        $this->em->persist($friend);
        $this->em->flush();
    }
}