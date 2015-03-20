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

    /**
     * Completely remoed a thing and all it's related entities (Ownership, Rentals, Actions)
     * from the database.
     *
     * @param Thing $thing
     */
    public function remove(Thing $thing) {
        $this->removeOwnerships($thing);
        $this->removeRentals($thing);
        $this->removeFunctions($thing);
        $this->removeStatusUpdates($thing);

        $this->em->remove($thing);
        $this->em->flush();
    }

    /**
     * @param Thing $thing
     */
    private function removeFunctions(Thing $thing)
    {
        /** @var Action $function */
        foreach ($thing->getFunctions() as $function) {

            /** @var Parameter $param */
            foreach ($function->getParameters() as $param) {

                /** @var AbstractConstraint $constraint */
                foreach ($param->getConstraints() as $constraint) {
                    $constraint->setFunctionParameter(null);
                    $this->em->remove($constraint);
                }

                $param->setAction(null);
                $this->em->remove($param);
                $param->getConstraints()->clear();
            }

            $function->setThing(null);
            $this->em->remove($function);
            $function->getParameters()->clear();
        }

        $this->em->remove($function);
        $thing->getFunctions()->clear();
    }

    /**
     * @param Thing $thing
     */
    private function removeRentals(Thing $thing)
    {
        /** @var Rental $rental */
        foreach ($thing->getRentals() as $rental) {
            $rental->getThing()->removeRental($rental);
            $rental->getUserFrom()->removeThingsRent($rental);
            $rental->getUserFrom()->removeThingsLent($rental);
            $rental->getUserTo()->removeThingsRent($rental);
            $rental->getUserTo()->removeThingsLent($rental);

            $this->em->persist($rental->getUserFrom());
            $this->em->persist($rental->getUserTo());
            $this->em->remove($rental);
        }
    }

    /**
     * @param Thing $thing
     */
    private function removeStatusUpdates(Thing $thing)
    {
        /** @var ThingStatusUpdate $update */
        foreach ($thing->getStatusUpdates() as $update) {
            $update->setThing(null);
            $this->em->remove($update);
        }
    }

    /**
     * @param Thing $thing
     */
    private function removeOwnerships(Thing $thing)
    {
        /** @var Ownership $ownership */
        $ownership = $thing->getOwnership();
        $ownership->getOwner()->removeOwnership($ownership);
        $this->em->remove($ownership);
    }
}