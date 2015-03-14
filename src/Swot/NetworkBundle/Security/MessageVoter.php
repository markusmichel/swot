<?php

namespace Swot\NetworkBundle\Security;


use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\Message;
use Swot\NetworkBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageVoter implements VoterInterface {

    const DELETE      = 'delete';

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
            self::DELETE,
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
        $supportedClass = 'Swot\NetworkBundle\Entity\Message';
        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * Checks if the current user has the rights to access the thing.
     *
     * This method returns one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token A TokenInterface instance
     * @param Message $message
     * @param array $attributes An array of attributes associated with the method being invoked
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $message, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($message))) {
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

        /** @var User $currentUser */
        $currentUser = $token->getUser();

        // Check if the user is logged in and the user exists
        if(!$currentUser instanceof UserInterface || $message === null) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::DELETE:
                if($message->getFrom() === $currentUser || $message->getTo() === $currentUser)
                    return VoterInterface::ACCESS_GRANTED;
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}