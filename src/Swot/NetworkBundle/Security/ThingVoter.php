<?php

namespace Swot\NetworkBundle\Security;


use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ThingVoter implements VoterInterface {

    const ADMIN = 'admin';
    const ACCESS = 'access';

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return bool true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::ADMIN,
            self::ACCESS,
        ));
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Swot\NetworkBundle\Entity\Thing';
        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * Checks if the current user has the rights to access the thing.
     *
     * This method returns one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token A TokenInterface instance
     * @param Thing $thing
     * @param array $attributes An array of attributes associated with the method being invoked
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $thing, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($thing))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var User $user */
        $user = $token->getUser();

        // Check if the user is logged in and the thing exists
        if(!$user instanceof UserInterface || $thing === null || $thing->getOwnership() === null) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            // Check if the user is the owner of the thing
            case self::ADMIN:
                if($this->isOwner($user, $thing)) return VoterInterface::ACCESS_GRANTED;
                break;

            // Check if user has permission to show the thing
            // He has permission if he is the owner or the thing is lent to the user
            // @todo: check if the thing is public or restricted + owner is a friend
            case self::ACCESS:
                if($this->isOwner($user, $thing) || $this->thingIsLentToUser($user, $thing)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }

    /**
     * @param $user User
     * @param $thing Thing
     * @return bool
     */
    private function isOwner($user, $thing) {
        return $thing->getOwnership()->getOwner() === $user;
    }

    /**
     * @param $user User
     * @param $thing Thing
     * @return bool
     */
    private function thingIsLentToUser($user, $thing) {
        // Check if the thing is lent to the user
        $isThingLent = false;
        /** @var Rental $rental */
        foreach($thing->getRentals() as $rental) {
            if($user->getThingsLent()->contains($rental)) {
                $isThingLent = true;
                break;
            }
        }

        return $isThingLent;
    }
}