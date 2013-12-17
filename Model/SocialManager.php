<?php

namespace DCS\OAuthBundle\Model;

use Doctrine\ORM\EntityManager;
use DCS\OAuthBundle\Entity\UserSocialAuth;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class SocialManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $em, EntityRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * Crea una nuova istanza della classe social
     *
     * @return DCS\OAuthBundle\Entity\UserSocialAuth
     */
    public function createUserSocial()
    {
        $class = $this->repository->getClassName();
        return new $class();
    }

    /**
     * Crea o aggiorna l'utente social
     *
     * @param \DCS\OAuthBundle\Entity\UserSocialAuth $userSocialAuth
     * @return \DCS\OAuthBundle\Entity\UserSocialAuth
     */
    public function updateUserSocialAuth(UserSocialAuth $userSocialAuth)
    {
        $this->em->persist($userSocialAuth);
        $this->em->flush();

        return $userSocialAuth;
    }

    public function findByProviderAndUid($provider, $uid)
    {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('usa, u')
            ->from($this->repository->getClassName(), 'usa')
            ->join('usa.user', 'u')
            ->where('usa.provider = :usa_provider')
            ->setParameter('usa_provider', $provider)
            ->andWhere('usa.uid = :usa_uid')
            ->setParameter('usa_uid', $uid)
        ;

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
