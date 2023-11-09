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
	 * @param string $locationSite - The location site
	 * @param string $name - The name of the event contains this string
	 * @param \DateTime $startDateTime - The start date time
	 * @param \DateTime $endDateTime - The end date time
	 * @param bool $userIsOrganizer - The user is organizer
	 * @param bool $userIsRegistered - The user is registered to the event
	 * @param bool $userIsNotRegistered - The user is not registered to the event
	 * @param bool $stateIsPassed - The state of the event is passed
	 * @return Event[] - Returns an array of Event objects filtered by parameters
	 */
	public function findByFilters(
		string $locationSite,
		?string $name,
		?\DateTime $startDateTime,
		?\DateTime $endDateTime,
		bool $userIsOrganizer,
		bool $userIsRegistered,
		bool $userIsNotRegistered,
		bool $stateIsPassed
	): array
	{
		// Check if the location site is set
		if ($locationSite !== 'Tous') {
			// If the location site is set, add it to the query
			$query = $this->createQueryBuilder('e')
				->andWhere('e.locationSiteEvent = :locationSite')
				->setParameter('locationSite', $locationSite);
		} else {
			// If the location site is not set, do not add it to the query
			$query = $this->createQueryBuilder('e');
		}

		// Check if the name is set
		if ($name !== '') {
			// If the name is set, add it to the query
			$query = $query
				->andWhere('e.name LIKE :name')
				->setParameter('name', '%' . $name . '%');
		}

		// Check if the start date time is set
		if ($startDateTime !== null) {
			// If the start date time is set, add it to the query
			$query = $query
				->andWhere('e.startDateTime >= :startDateTime')
				->setParameter('startDateTime', $startDateTime);
		}

		// Check if the end date time is set
		if ($endDateTime !== null) {
			// If the end date time is set, add it to the query
			$query = $query
				->andWhere('e.startDateTime <= :endDateTime')
				->setParameter('endDateTime', $endDateTime);
		}

		// Check if the user is organizer
		if ($userIsOrganizer) {
			// If the user is organizer, add it to the query
			$query = $query
				->andWhere('e.user = :user')
				->setParameter('user', $this->getUser());
		}

		// Check if the user is registered to the event
		if ($userIsRegistered) {
			// If the user is registered to the event, add it to the query
			$query = $query
				->andWhere(':user MEMBER OF e.users')
				->setParameter('user', $this->getUser());
		}

		// Check if the user is not registered to the event
		if ($userIsNotRegistered) {
			// If the user is not registered to the event, add it to the query
			$query = $query
				->andWhere(':user NOT MEMBER OF e.users')
				->setParameter('user', $this->getUser());
		}

		// Check if the state is passed
		if ($stateIsPassed) {
			// If the state is passed, add it to the query
			$query = $query
				->andWhere('e.state = :state')
				->setParameter('state', 'Passed');
		}

		// Return the query
		return $query
			->orderBy('e.startDateTime', 'ASC')
			->getQuery()
			->getResult();
	}

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
