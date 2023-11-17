<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\State;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

	/**
	 * @param int $id
	 * @return Event|null
	 * @throws NonUniqueResultException
	 */
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

	/**
	 * @param int $id
	 * @return Event
	 */
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

	/**
	 * @param int $locationSite - The location site
	 * @param string|null $name - The name of the event contains this string
	 * @param \DateTimeInterface|null $startDateTime - The start date time
	 * @param \DateTimeInterface|null $endDateTime - The end date time
	 * @param User $user - The user
	 * @param bool $userIsOrganizer - The user is organizer
	 * @param bool $userIsRegistered - The user is registered to the event
	 * @param bool $userIsNotRegistered - The user is not registered to the event
	 * @param bool $stateIsPassed - The state of the event is passed
	 * @return Event[] - Returns an array of Event objects filtered by parameters
	 */
	public function findByFilters(
		int $locationSite,
		?string $name,
		?\DateTimeInterface $startDateTime,
		?\DateTimeInterface $endDateTime,
		User $user,
		bool $userIsOrganizer,
		bool $userIsRegistered,
		bool $userIsNotRegistered,
		bool $stateIsPassed
	): array
	{
		// Check if the location site is set
		if ($locationSite !== 0) {
			// If the location site is set, add it to the query
			$query = $this->createQueryBuilder('e')
				->orWhere('e.locationSiteEvent = :locationSite')
				->setParameter('locationSite', $locationSite);
		} else {
			// If the location site is not set, do not add it to the query
			$query = $this->createQueryBuilder('e');
		}

		// Check if the name is set
		if ($name !== null) {
			// If the name is set, add it to the query
			$query = $query
				->orWhere('e.name LIKE :name')
				->setParameter('name', '%' . $name . '%');
		}

		// Check if only the start date time is set
		if ($startDateTime !== null and $endDateTime === null) {

			// If the start date time is set, add it to the query
			$query = $query
				->andWhere('e.startDateTime >= :startDateTime')
				->setParameter('startDateTime', $startDateTime);
		}

		// Check if only the end date time is set
		if ($endDateTime !== null and $startDateTime === null) {
			// If the end date time is set, add it to the query
			$query = $query
				->andWhere('e.startDateTime <= :endDateTime')
				->setParameter('endDateTime', $endDateTime);
		}

        // Check if the start date and the end date time is set
        if ($startDateTime !== null and $endDateTime !== null) {
            $query = $query
                ->andWhere('e.startDateTime >= :startDateTime')
                ->andWhere('e.endDateTime <= :endDateTime')
                ->setParameter('startDateTime', $startDateTime)
                ->setParameter('endDateTime', $endDateTime);
        }

		// Check if the user is the organizer of the event
		if ($userIsOrganizer) {
			// If the user is the organizer of the event, add it to the query
			$query = $query
				->orWhere('e.user = :user')
				->setParameter('user', $user);
		}

		// Check if the user is registered to the event
		if ($userIsRegistered) {
			// If the user is registered to the event, add it to the query
			$query = $query
				->orWhere(':user MEMBER OF e.users')
				->setParameter('user', $user);
		}

		// Check if the user is not registered to the event
		if ($userIsNotRegistered) {
			// If the user is not registered to the event, add it to the query
			$query = $query
				->orWhere(':user NOT MEMBER OF e.users')
				->setParameter('user', $user);
		}

		// Check if the state is passed
		if ($stateIsPassed) {
			// If the state is passed, add it to the query
			$query = $query
				->orWhere('e.state = :state')
				->setParameter('state', 'Passed');
		}

		// Return the query
		return $query
			->orderBy('e.startDateTime', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * @param int $locationSite - The location site
	 * @param string|null $name - The name of the event contains this string
	 * @param \DateTimeInterface|null $startDateTime - The start date time
	 * @param \DateTimeInterface|null $endDateTime - The end date time
	 * @param State|null $state - The state of the event
	 * @return Event[] - Returns an array of Event objects filtered by parameters
	 */
	public function findByAdminFilters(
		int $locationSite,
		?string $name,
		?\DateTimeInterface $startDateTime,
		?\DateTimeInterface $endDateTime,
		?State $state
	): array
	{
		// Check if the location site is set
		if ($locationSite !== 0) {
			// If the location site is set, add it to the query
			$query = $this->createQueryBuilder('e')
				->orWhere('e.locationSiteEvent = :locationSite')
				->setParameter('locationSite', $locationSite);
		} else {
			// If the location site is not set, do not add it to the query
			$query = $this->createQueryBuilder('e');
		}

		// Check if the name is set
		if ($name !== null) {
			// If the name is set, add it to the query
			$query = $query
				->orWhere('e.name LIKE :name')
				->setParameter('name', '%' . $name . '%');
		}

		// Check if the start date time is set
		if ($startDateTime !== null) {
			// If the start date time is set, add it to the query
			$query = $query
				->orWhere('e.startDateTime >= :startDateTime')
				->setParameter('startDateTime', $startDateTime);

		}

		// Check if the end date time is set
		if ($endDateTime !== null) {
			// If the end date time is set, add it to the query
			$query = $query
				->orWhere('e.startDateTime <= :endDateTime')
				->setParameter('endDateTime', $endDateTime);
		}

		// Check if the state is set
		if ($state !== null) {
			// If the state is set, add it to the query
			$query = $query
				->orWhere('e.state = :state')
				->setParameter('state', $state);
		}

		// Return the query
		return $query
			->orderBy('e.startDateTime', 'ASC')
			->getQuery()
			->getResult();
	}
}
