<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findAllNotArchived(): array
    {
        $oneMonthAgo = new \DateTime();
        $oneMonthAgo->modify('-1 month');

        return $this->createQueryBuilder('e')
            ->where('e.startDateTime > :oneMonthAgo')
            ->setParameter('oneMonthAgo', $oneMonthAgo)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByIdNotArchived(int $id): ?Event
    {
        $oneMonthAgo = new \DateTime();
        $oneMonthAgo->modify('-1 month');
        return $this->createQueryBuilder('e')
            ->where('(e.startDateTime > :oneMonthAgo) AND (e.id = :eventId)')
            ->setParameter('oneMonthAgo', $oneMonthAgo)
            ->setParameter('eventId', $id)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneByIdArchived(int $id): Event
    {
        $oneMonthAgo = new \DateTime();
        $oneMonthAgo->modify('-1 month');

        return $this->createQueryBuilder('e')
            ->where('(e.startDateTime <= :oneMonthAgo) AND (e.id = :eventId)')
            ->setParameter('oneMonthAgo', $oneMonthAgo)
            ->setParameter('eventId', $id)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
