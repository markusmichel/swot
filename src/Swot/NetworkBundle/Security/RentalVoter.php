<?php

namespace Swot\NetworkBundle\Security;


use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RentalVoter implements VoterInterface {

    const QUIT = 'quit';

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
            self::QUIT,
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
        $supportedClass = 'Swot\NetworkBundle\Entity\Rental';
        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token A TokenInterface instance
     * @param Rental $rental
     * @param array $attributes An array of attributes associated with the method being invoked
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $rental, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($rental))) {
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
        if(!$user instanceof UserInterface || $rental === null) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            // Check if the user is involved in the rental
            case self::QUIT:
                if($rental->getUserTo() === $user || $rental->getUserFrom() === $user) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}