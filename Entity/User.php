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
abstract class User extends BaseUser
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $oauthInfos;

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

        $this->oauthInfos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add userOAuthInfo
     *
     * @param \DCS\OAuthBundle\Entity\UserOAuthInfo $userOAuthInfo
     * @return User
     */
    public function addUserOAuthInfo(\DCS\OAuthBundle\Entity\UserOAuthInfo $userOAuthInfo)
    {
        $this->oauthInfos[] = $userOAuthInfo;

        return $this;
    }

    /**
     * Remove userOAuthInfo
     *
     * @param \DCS\OAuthBundle\Entity\UserOAuthInfo $userOAuthInfo
     */
    public function removeUserOAuthInfo(\DCS\OAuthBundle\Entity\UserOAuthInfo $userOAuthInfo)
    {
        $this->oauthInfos->removeElement($userOAuthInfo);
    }

    /**
     * Get oauthInfos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserOAuthInfo()
    {
        if (null == $this->oauthInfos)
            $this->oauthInfos = new \Doctrine\Common\Collections\ArrayCollection();

        return $this->oauthInfos;
   }

    /**
     * Set loginProvider
     *
     * @param string $loginProvider
     * @return \DCS\OAuthBundle\Entity\User
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

        foreach ($this->getUserOAuthInfo() as $userOAuthInfo) {
            if ($userOAuthInfo->getProvider() === $this->loginProvider) {
                return $userOAuthInfo->getUsername();
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