<?php

namespace Bukatov\ApiTokenBundle\Security\Authentication\Provider;

use Bukatov\ApiTokenBundle\Security\User\ApiTokenUserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GetApiTokenProvider extends DaoAuthenticationProvider
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var int|null
     */
    private $lifetime;

    /**
     * @var int|null
     */
    private $idleTime;

    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, EncoderFactoryInterface $encoderFactory, $hideUserNotFoundExceptions = true)
    {
        parent::__construct($userProvider, $userChecker, $providerKey, $encoderFactory, $hideUserNotFoundExceptions);

        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        $authenticatedToken = parent::authenticate($token);

        /* @var ApiTokenUserProviderInterface $userProvider */
        $userProvider = $this->userProvider;

        $userProvider->createOrUpdateApiTokenForUser($authenticatedToken->getUser(), $this->lifetime, $this->idleTime);

        return $authenticatedToken;
    }

    /**
     * @param mixed $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * @param mixed $idleTime
     */
    public function setIdleTime($idleTime)
    {
        $this->idleTime = $idleTime;
    }

    public function supports(TokenInterface $token)
    {
        return $this->userProvider instanceof ApiTokenUserProviderInterface;
    }
}