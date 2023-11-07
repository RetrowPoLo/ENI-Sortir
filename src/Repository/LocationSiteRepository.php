<?php

namespace App\Repository;

use App\Entity\LocationSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocationSite>
 *
 * @method LocationSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationSite[]    findAll()
 * @method LocationSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationSite::class);
    }

//    /**
//     * @return LocationSite[] Returns an array of LocationSite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LocationSite
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
