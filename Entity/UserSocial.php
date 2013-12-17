<?php

namespace DCS\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\MappedSuperclass
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="usernameCanonical",
 *          column=@ORM\Column(
 *              name     = "username_canonical",
 *              type     = "string",
 *              length   = 255,
 *              unique   = false
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              name     = "email_canonical",
 *              type     = "string",
 *              length   = 255,
 *              unique   = false
 *          )
 *      )
 * })
 */
abstract class UserSocial extends BaseUser
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $socialsAuth;

    /**
     * @var string
     *
     * @ORM\Column(name="login_provider", type="string", nullable=true)
     */
    protected $loginProvider;

    /**
     * @var string
     */
    protected $loginProviderUsername;

    public function __construct()
    {
        parent::__construct();

        $this->socialsAuth = new \Doctrine\Common\Collections\ArrayCollection();
        $this->loginProviderUsername = '';
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->loginProvider,
            $this->getLoginProviderUsername(),
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = array_merge(unserialize($serialized), array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->loginProvider,
            $this->loginProviderUsername,
        ) = $data;
 }

    /**
     * Add socialsAuth
     *
     * @param \DCS\OAuthBundle\Entity\UserSocialAuth $socialsAuth
     * @return User
     */
    public function addSocialsAuth(\DCS\OAuthBundle\Entity\UserSocialAuth $socialsAuth)
    {
        $this->socialsAuth[] = $socialsAuth;

        return $this;
    }

    /**
     * Remove socialsAuth
     *
     * @param \DCS\OAuthBundle\Entity\UserSocialAuth $socialsAuth
     */
    public function removeSocialsAuth(\DCS\OAuthBundle\Entity\UserSocialAuth $socialsAuth)
    {
        $this->socialsAuth->removeElement($socialsAuth);
    }

    /**
     * Get socialsAuth
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSocialsAuth()
    {
        if (null == $this->socialsAuth)
            $this->socialsAuth = new \Doctrine\Common\Collections\ArrayCollection();

        return $this->socialsAuth;
   }

    /**
     * Set loginProvider
     *
     * @param string $loginProvider
     * @return \DCS\OAuthBundle\Entity\UserSocial
     */
    public function setLoginProvider($loginProvider)
    {
        $this->loginProvider = $loginProvider;

        return $this;
    }

    /**
     * Get loginProvider
     *
     * @return string
     */
    public function getLoginProvider()
    {
        return $this->loginProvider;
    }

    public function getUsername()
    {
        if (!empty($this->username))
            return $this->username;

        if (!empty($this->loginProvider) && !empty($this->loginProviderUsername))
            return $this->loginProviderUsername;

        return $this->getLoginProviderUsername();
    }

    private function getLoginProviderUsername()
    {
        if (!empty($this->loginProviderUsername))
            return $this->loginProviderUsername;

        foreach ($this->getSocialsAuth() as $socialAuth) {
            if ($socialAuth->getProvider() === $this->loginProvider) {
                return $socialAuth->getUsername();
            }
        }

        return '';
    }

    public function setUsernameCanonical($usernameCanonical)
    {
        if (empty($this->username)) {
            $this->usernameCanonical = '';
        } else {
            parent::setUsernameCanonical($usernameCanonical);
        }

        return $this;
    }
}