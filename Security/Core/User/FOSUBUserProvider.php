<?php

namespace DCS\OAuthBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use DCS\OAuthBundle\Model\SocialManager;
use FOS\UserBundle\Model\UserManagerInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * @var \DCS\OAuthBundle\Model\SocialManager
     */
    protected $socialManager;

    public function __construct(UserManagerInterface $userManager, SocialManager $socialManager)
    {
        parent::__construct($userManager, array());

        $this->socialManager = $socialManager;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $provider = $response->getResourceOwner()->getName();
        $uid = $response->getUsername();

        $userSocialAuth = $this->socialManager->findByProviderAndUid($provider, $uid);

        if (null === $userSocialAuth) {
            $user->addRole('ROLE_'.strtoupper($provider));

            $userSocialAuth = $this->socialManager->createUserSocial();
            $userSocialAuth->setUser($user);
            $userSocialAuth->setProvider($provider);
            $userSocialAuth->setUid($uid);
            $userSocialAuth->setUsername($response->getNickname());
            $userSocialAuth->setAccessToken($response->getAccessToken());
            $userSocialAuth->setRaw(serialize($response->getResponse()));

            $user->addSocialsAuth($userSocialAuth);

            $this->userManager->updateUser($user);
        } else {
            $userSocialAuth->setUser($user);
            $userSocialAuth->setUsername($response->getNickname());
            $userSocialAuth->setAccessToken($response->getAccessToken());
            $userSocialAuth->setRaw(serialize($response->getResponse()));

            $this->socialManager->updateUserSocialAuth($userSocialAuth);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $provider = $response->getResourceOwner()->getName();
        $uid = $response->getUsername();

        $userSocialAuth = $this->socialManager->findByProviderAndUid($provider, $uid);

        // Eventually it will be an object of type User
        $user = null;

        if (null === $userSocialAuth) {
            if (null !== $email = $response->getEmail()) {
                // Check if exists a user with the email found from the provider
                $user = $this->userManager->findUserByEmail($email);
            }

            if (null === $user) {
                // No users found. It will be created
                $user = $this->userManager->createUser();
                $user->setUsername('');
                $user->setEmail('');
                $user->setPassword('');
                $user->setEnabled(true);
            }

            $user->setLoginProvider($provider);
            $user->addRole('ROLE_'.strtoupper($provider));

            // Add or update user data
            $this->userManager->updateUser($user);

            $userSocialAuth = $this->socialManager->createUserSocial();
            $userSocialAuth->setUser($user);
            $userSocialAuth->setProvider($provider);
            $userSocialAuth->setUid($uid);
            $userSocialAuth->setUsername($response->getNickname());
            $userSocialAuth->setAccessToken($response->getAccessToken());
            $userSocialAuth->setRaw(serialize($response->getResponse()));

            $user->addSocialsAuth($userSocialAuth);

            $this->userManager->updateUser($user);

        } else {
            $user = $userSocialAuth->getUser();

            // Update login provider
            if ($provider !== $user->getLoginProvider()) {
                $user->setLoginProvider($provider);
                $this->userManager->updateUser($user);
            }

            $userSocialAuth->setAccessToken($response->getAccessToken());
            $userSocialAuth->setRaw(serialize($response->getResponse()));

            $this->socialManager->updateUserSocialAuth($userSocialAuth);
        }

        return $user;
    }
}
