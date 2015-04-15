<?php

namespace Swot\NetworkBundle\Services\Manager;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\FormMapperBundle\Entity\Action;
use Swot\FormMapperBundle\Entity\Parameter\Parameter;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingStatusUpdate;
use Swot\NetworkBundle\Entity\User;

/**
 *
 *
 * Class ThingManager
 * @package Swot\NetworkBundle\Services\Manager
 *
 * @DI\Service("swot.manager.thing")
 */
class ThingManager {

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

    public function createOwnership(Thing $thing, User $user) {
        $ownership = new Ownership();
        $ownership->setThing($thing);
        $ownership->setOwner($user);
        $user->addOwnership($ownership);
        $thing->setOwnership($ownership);

        return $ownership;
    }

    /**
     * Completely remoed a thing and all it's related entities (Ownership, Rentals, Actions)
     * from the database.
     *
     * @param Thing $thing
     */
    public function remove(Thing $thing) {
        $this->em->remove($thing);
        $this->em->flush();
    }

    /**
     * @param Thing $thing
     */
    public function removeFunctions(Thing $thing)
    {
        /** @var Action $func */
        foreach($thing->getFunctions() as $func){
            $this->em->remove($func);
        }
        $thing->getFunctions()->clear();
        $this->em->persist($thing);
        $this->em->flush();
    }
}