<?php

namespace DCS\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class UserOAuthInfo implements UserInterface
{
    /**
     * @var \DCS\OAuthBundle\Entity\User
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\Column(name="provider", type="string", nullable=false)
     */
    protected $provider;

    /**
     * @ORM\Column(name="uid", type="string", nullable=false)
     */
    protected $uid;

    /**
     * @ORM\Column(name="access_token", type="string", nullable=false)
     */
    protected $accessToken;

    /**
     * @ORM\Column(name="username", type="string", nullable=false)
     */
    protected $username;

    /**
     * @ORM\Column(name="raw", type="text", nullable=true)
     */
    protected $raw;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return UserOAuthInfo
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set uid
     *
     * @param string $uid
     * @return UserOAuthInfo
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return UserOAuthInfo
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     * @return UserOAuthInfo
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set raw
     *
     * @param string $raw
     * @return UserOAuthInfo
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Get raw
     *
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserOAuthInfo
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     * @return UserOAuthInfo
     */
    public function setUser(\FOS\UserBundle\Model\UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    public function eraseCredentials()
    {
        return true;
    }

    public function getPassword()
    {
        return null;
    }

    public function getRoles()
    {
        return $this->user->getRoles();
    }

    public function getSalt()
    {
        return null;
    }
}