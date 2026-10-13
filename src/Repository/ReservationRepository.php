<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findUpcomingByClient($user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.utilisateur = :user')
            ->andWhere('r.datetimeReservation >= :today')
            ->setParameter('user', $user)
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('r.datetimeReservation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllUpcoming(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.datetimeReservation >= :today')
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('r.datetimeReservation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
