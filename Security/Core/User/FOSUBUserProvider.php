<?php

namespace DCS\OAuthBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use DCS\OAuthBundle\Model\OAuthManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use DCS\OAuthBundle\Events;

class FOSUBUserProvider extends BaseClass
{
    /**
     * @var \DCS\OAuthBundle\Model\OAuthManager
     */
    protected $oauthManager;

    /**
     * @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher
     */
    private $dispatcher;

    public function __construct(
        UserManagerInterface $userManager,
        OAuthManager $oauthManager,
        ContainerAwareEventDispatcher $dispatcher
    ) {
        parent::__construct($userManager, array());

        $this->oauthManager = $oauthManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $provider = $response->getResourceOwner()->getName();
        $uid = $response->getUsername();

        $userOAuthInfo = $this->oauthManager->findByProviderAndUid($provider, $uid);

        if (null === $userOAuthInfo) {
            $user->addRole('ROLE_'.strtoupper($provider));

            $userOAuthInfo = $this->oauthManager->createUserOAuthInfo();
            $userOAuthInfo->setUser($user);
            $userOAuthInfo->setProvider($provider);
            $userOAuthInfo->setUid($uid);
            $userOAuthInfo->setUsername($response->getNickname());
            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $user->addUserOAuthInfo($userOAuthInfo);

            $this->userManager->updateUser($user);
        } else {
            $userOAuthInfo->setUser($user);
            $userOAuthInfo->setUsername($response->getNickname());
            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $this->oauthManager->updateUserOAuthInfo($userOAuthInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $provider = $response->getResourceOwner()->getName();
        $uid = $response->getUsername();

        $userOAuthInfo = $this->oauthManager->findByProviderAndUid($provider, $uid);

        // Eventually it will be an object of type User
        $user = null;

        if (null === $userOAuthInfo) {
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

            $userOAuthInfo = $this->oauthManager->createUserOAuthInfo();
            $userOAuthInfo->setUser($user);
            $userOAuthInfo->setProvider($provider);
            $userOAuthInfo->setUid($uid);
            $userOAuthInfo->setUsername($response->getNickname());
            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $user->addUserOAuthInfo($userOAuthInfo);

            $this->userManager->updateUser($user);

        } else {
            $user = $userOAuthInfo->getUser();

            // Update login provider
            if ($provider !== $user->getLoginProvider()) {
                $user->setLoginProvider($provider);
                $this->userManager->updateUser($user);
            }

            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $this->oauthManager->updateUserOAuthInfo($userOAuthInfo);
        }

        return $user;
    }
}
