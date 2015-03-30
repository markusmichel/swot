<?php

namespace Swot\NetworkBundle\Security;


use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ThingProvider implements UserProviderInterface {

    protected $repo;

    public function __construct(ThingRepository $repo) {
        $this->repo = $repo;
    }

    public function getThingIdForAccessToken($accessToken) {
        /** @var Thing $thing */
        $thing = $this->repo->findOneBy(array(
            "networkAccessToken" => $accessToken
        ));

        if($thing === null) return null;

        return $thing->getId();
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return $this->repo->findOneById($username);
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
//        return 'Symfony\Component\Security\Core\User\User' === $class;
        return 'Swot\NetworkBundle\Entity\Thing' === $class;
    }
}