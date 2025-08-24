<?php

namespace App\Repository;

use App\Entity\LoginEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginEvent>
 */
class LoginEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginEvent::class);
    }

    /**
     * Exemple : compter les connexions d’aujourd’hui
     */
    public function countTodayLogins(): int
    {
        $start = new \DateTimeImmutable('today 00:00');
        $end   = new \DateTimeImmutable('tomorrow 00:00');

        return (int)$this->createQueryBuilder('e')
            ->select('COUNT(DISTINCT e.user)')
            ->andWhere('e.loggedAt >= :s')
            ->andWhere('e.loggedAt < :e')
            ->setParameter('s', $start)
            ->setParameter('e', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
