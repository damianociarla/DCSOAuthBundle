<?php

namespace DCS\OAuthBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use DCS\OAuthBundle\Entity\User;

class UserEvent extends Event
{
    /**
     * @var \DCS\OAuthBundle\Entity\User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
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
}