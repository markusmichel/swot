<?php

namespace Swot\NetworkBundle\Security;


use Swot\NetworkBundle\Entity\Thing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ThingAuthenticator implements SimplePreAuthenticatorInterface {

    protected $thingProvider;

    public function __construct(ThingProvider $userProvider)
    {
        $this->thingProvider = $userProvider;
    }

    /**
     * Called third!
     *
     * @param TokenInterface $token
     * @param ThingProvider $userProvider
     * @param $providerKey
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $networkAccessToken = $token->getCredentials();
        $username = $this->thingProvider->getThingIdForAccessToken($networkAccessToken);

        if (!$username) {
            throw new AuthenticationException(
                sprintf('API Key "%s" does not exist.', $networkAccessToken)
            );
        }

        /** @var Thing $thing */
        $thing = $this->thingProvider->loadUserByUsername($username);

        return new ThingAuthenticatedToken(
            $thing,
            $networkAccessToken,
            $providerKey,
            array("IS_AUTHENTICATED_ANONYMOUSLY")
        );
    }

    /**
     * Called second!
     *
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof ThingAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * Called first!
     *
     * @param Request $request
     * @param $providerKey
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        // look for an apikey query parameter
//        $apiKey = $request->query->get('apikey');

        // or if you want to use an "apikey" header, then do something like this:
         $networkAccessToken = $request->headers->get('access_token');

        if (!$networkAccessToken) {
            throw new BadCredentialsException('No API key found');
        }

        return new ThingAuthenticatedToken(
            'anon.',
            $networkAccessToken,
            $providerKey
        );
    }
}