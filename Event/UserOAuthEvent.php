<?php

namespace DCS\OAuthBundle\Event;

use DCS\OAuthBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use DCS\OAuthBundle\Entity\UserOAuthInfo;
use DCS\OAuthBundle\Event\UserEvent;

class UserOAuthEvent extends UserEvent
{
    /**
     * @var \DCS\OAuthBundle\Entity\UserOAuthInfo
     */
    private $userOAuthInfo;

    public function __construct(User $user, UserResponseInterface $userResponse, UserOAuthInfo $userOAuthInfo)
    {
        parent::__construct($user, $userResponse);
        $this->userOAuthInfo = $userOAuthInfo;
    }

    /**
     * Get userOAuthInfo
     *
     * @return \DCS\OAuthBundle\Entity\UserOAuthInfo
     */
    public function getUserOAuthInfo()
    {
        return $this->userOAuthInfo;
    }
}