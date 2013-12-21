<?php

namespace DCS\OAuthBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use DCS\OAuthBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class UserEvent extends Event
{
    /**
     * @var \DCS\OAuthBundle\Entity\User
     */
    private $user;

    /**
     * @var \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface
     */
    private $userResponse;

    public function __construct(User $user, UserResponseInterface $userResponse)
    {
        $this->user = $user;
        $this->userResponse = $userResponse;
    }

    /**
     * Get user
     *
     * @return \DCS\OAuthBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get userResponse
     *
     * @return \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface
     */
    public function getUserResponse()
    {
        return $this->userResponse;
    }

    /**
     * Get name of the provider
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->userResponse->getResourceOwner()->getName();
    }
}