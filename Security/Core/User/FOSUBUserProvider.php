<?php

namespace DCS\OAuthBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use DCS\OAuthBundle\Model\OAuthManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use DCS\OAuthBundle\Events;
use DCS\OAuthBundle\Event;

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

            $this->dispatcher->dispatch(Events::BEFORE_SYNC_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
            $this->userManager->updateUser($user);
            $this->dispatcher->dispatch(Events::AFTER_SYNC_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
        } else {
            $userOAuthInfo->setUser($user);
            $userOAuthInfo->setUsername($response->getNickname());
            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $this->dispatcher->dispatch(Events::BEFORE_UPDATE_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
            $this->oauthManager->updateUserOAuthInfo($userOAuthInfo);
            $this->dispatcher->dispatch(Events::AFTER_UPDATE_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
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

                $this->dispatcher->dispatch(Events::BEFORE_CREATE_NEW_USER, new Event\UserEvent($user, $response));
            } else {
                $this->dispatcher->dispatch(Events::BEFORE_UPDATE_EXISTING_USER, new Event\UserEvent($user, $response));
            }

            $user->setLoginProvider($provider);
            $user->addRole('ROLE_'.strtoupper($provider));

            // Add or update user data
            $this->userManager->updateUser($user);
            $this->dispatcher->dispatch(Events::AFTER_PERSIST_USER, new Event\UserEvent($user, $response));

            $userOAuthInfo = $this->oauthManager->createUserOAuthInfo();
            $userOAuthInfo->setUser($user);
            $userOAuthInfo->setProvider($provider);
            $userOAuthInfo->setUid($uid);
            $userOAuthInfo->setUsername($response->getNickname());
            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $user->addUserOAuthInfo($userOAuthInfo);

            $this->dispatcher->dispatch(Events::BEFORE_JOIN_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
            $this->userManager->updateUser($user);
            $this->dispatcher->dispatch(Events::AFTER_JOIN_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));

        } else {
            $user = $userOAuthInfo->getUser();

            // Update login provider
            if ($provider !== $user->getLoginProvider()) {
                $user->setLoginProvider($provider);

                $this->dispatcher->dispatch(Events::BEFORE_UPDATE_LOGIN_PROVIDER, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
                $this->userManager->updateUser($user);
                $this->dispatcher->dispatch(Events::AFTER_UPDATE_LOGIN_PROVIDER, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
            }

            $userOAuthInfo->setAccessToken($response->getAccessToken());
            $userOAuthInfo->setRaw(serialize($response->getResponse()));

            $this->dispatcher->dispatch(Events::BEFORE_UPDATE_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
            $this->oauthManager->updateUserOAuthInfo($userOAuthInfo);
            $this->dispatcher->dispatch(Events::AFTER_UPDATE_OAUTH_INFO, new Event\UserOAuthEvent($user, $response, $userOAuthInfo));
        }

        return $user;
    }
}
