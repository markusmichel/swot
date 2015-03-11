<?php

namespace Swot\NetworkBundle\Security;


use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter implements VoterInterface {

    const SHOW      = 'show';
    const FRIEND    = 'friend';

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
            self::SHOW,
            self::FRIEND,
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
        $supportedClass = 'Swot\NetworkBundle\Entity\User';
        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * Checks if the current user has the rights to access the thing.
     *
     * This method returns one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token A TokenInterface instance
     * @param User $user
     * @param array $attributes An array of attributes associated with the method being invoked
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $user, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($user))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for SHOW'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var User $user */
        $currentUser = $token->getUser();

        // Check if the user is logged in and the user exists
        if(!$currentUser instanceof UserInterface || $user === null) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::SHOW:
                if($user->isFriendOf($currentUser) || $user === $currentUser)
                    return VoterInterface::ACCESS_GRANTED;
                break;
            case self::FRIEND:
                if($user->isFriendOf($currentUser) || $user === $currentUser)
                    return VoterInterface::ACCESS_GRANTED;
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}