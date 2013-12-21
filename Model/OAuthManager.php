<?php

namespace DCS\OAuthBundle\Model;

use Doctrine\ORM\EntityManager;
use DCS\OAuthBundle\Entity\UserOAuthInfo;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class OAuthManager
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
     * Create a new instance of UserOAuthInfo
     *
     * @return DCS\OAuthBundle\Entity\UserOAuthInfo
     */
    public function createUserOAuthInfo()
    {
        $class = $this->repository->getClassName();
        return new $class();
    }

    /**
     * Create or update an instance of UserOAuthInfo
     *
     * @param \DCS\OAuthBundle\Entity\UserOAuthInfo $userOAuthInfo
     * @return \DCS\OAuthBundle\Entity\UserOAuthInfo
     */
    public function updateUserOAuthInfo(UserOAuthInfo $userOAuthInfo)
    {
        $this->em->persist($userOAuthInfo);
        $this->em->flush();

        return $userOAuthInfo;
    }

    public function findByProviderAndUid($provider, $uid)
    {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('uoai, u')
            ->from($this->repository->getClassName(), 'uoai')
            ->join('uoai.user', 'u')
            ->where('uoai.provider = :uoai_provider')
            ->setParameter('uoai_provider', $provider)
            ->andWhere('uoai.uid = :uoai_uid')
            ->setParameter('uoai_uid', $uid)
        ;

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
