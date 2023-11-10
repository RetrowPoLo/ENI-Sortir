<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\LocationSite;
use App\Entity\State;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	public function __construct(private UserPasswordHasherInterface $passwordHasher)
	{
	}

	public function load(ObjectManager $manager): void
    {
		// Create a basic user
		$basicUser = new User();
		$basicUser->setEmail('user@user.com');
		$basicUser->setUsername('basic user');
		$basicUser->setFirstName('John');
		$basicUser->setName('Doe');
		$basicUser->setPhone('0123456789');
		$basicUser->setIsActive(true);
		$basicUser->setRoles(['ROLE_USER']);
		$basicUserPassword = $this->passwordHasher->hashPassword(
			$basicUser,
			'12345'
		);
		$basicUser->setPassword($basicUserPassword);

		// Persist that admin user to the database
		$manager->persist($basicUser);

		// Create an admin user
		$adminUser = new User();
		$adminUser->setEmail('admin@admin.com');
		$adminUser->setUsername('admin user');
		$adminUser->setFirstName('Jane');
		$adminUser->setName('Doe');
		$adminUser->setPhone('0123456789');
		$adminUser->setIsActive(true);
		$adminUser->setRoles(['ROLE_ADMIN']);
		$adminUserPassword = $this->passwordHasher->hashPassword(
			$adminUser,
			'12345'
		);
		$adminUser->setPassword($adminUserPassword);

		// Persist that admin user to the database
		$manager->persist($adminUser);

		// ====== Create 3 location sites ======
		// Create a location site named "La Roche sur Yon"
		$locationSite1 = new LocationSite();
		$locationSite1->setName('La Roche sur Yon');

		// Persist that location site to the database
		$manager->persist($locationSite1);

		// Create a location site named "Saint Herblain"
		$locationSite2 = new LocationSite();
		$locationSite2->setName('Saint Herblain');

		// Persist that location site to the database
		$manager->persist($locationSite2);

		// Create a location site named "Chartres de Bretagne"
		$locationSite3 = new LocationSite();
		$locationSite3->setName('Chartres de Bretagne');

		// Persist that location site to the database
		$manager->persist($locationSite3);

		// ====== Create 3 cities ======
		// Create a city named "Herblay"
		$city1 = new City();
		$city1->setName('Herblay');
		$city1->setZipcode('95220');

		// Persist that city to the database
		$manager->persist($city1);

		// Create a city named "Saint Herblain"
		$city2 = new City();
		$city2->setName('Saint Herblain');
		$city2->setZipcode('44800');

		// Persist that city to the database
		$manager->persist($city2);

		// Create a city named "Cherbourg"
		$city3 = new City();
		$city3->setName('Cherbourg');
		$city3->setZipcode('50100');

		// Persist that city to the database
		$manager->persist($city3);

		// ====== Create 3 locations ======
		// Create a location named "Terrain de foot"
		$location1 = new Location();
		$location1->setName('Terrain de foot');
		$location1->setStreet('Rue de la République');
		$location1->setLatitude(48.971);
		$location1->setLongitude(2.168);
		$location1->setCity($city1);

		// Persist that location to the database
		$manager->persist($location1);

		// Create a location named "Parc de la mairie"
		$location2 = new Location();
		$location2->setName('Parc de la mairie');
		$location2->setStreet('Rue de la Mairie');
		$location2->setLatitude(47.217);
		$location2->setLongitude(-1.648);
		$location2->setCity($city2);

		// Persist that location to the database
		$manager->persist($location2);

		// Create a location named "Bar de la plage"
		$location3 = new Location();
		$location3->setName('Bar de la plage');
		$location3->setStreet('Rue de la Plage');
		$location3->setLatitude(49.633);
		$location3->setLongitude(-1.616);
		$location3->setCity($city2);

		// Persist that location to the database
		$manager->persist($location3);

		// ====== Create 5 events ======
		// Create an event named "Match de foot"
		$event1 = new Event();
		$event1->setName('Match de foot');
		$event1->setStartDateTime(new \DateTime('2021-10-10 10:00:00'));
		$event1->setLimitDateInscription(new \DateTime('2021-10-09 10:00:00'));
		$event1->setEventInfo('Match de foot entre amis');
		$event1->setDuration(new \DateTime('2021-10-10 02:00:00'));
		$event1->setLocationSiteEvent($locationSite1);
		$event1->setNbInscriptionMax(10);
		$event1->setUser($basicUser);
		$event1->addUser($adminUser);
		$event1->setState(State::Open);

		// Persist that event to the database
		$manager->persist($event1);

		// Create an event named "Marche à la mairie"
		$event2 = new Event();
		$event2->setName('Marche à la mairie');
		$event2->setStartDateTime(new \DateTime('2021-10-10 10:00:00'));
		$event2->setLimitDateInscription(new \DateTime('2021-10-09 10:00:00'));
		$event2->setEventInfo('Marche à la mairie entre amis');
		$event2->setDuration(new \DateTime('2021-10-10 02:00:00'));
		$event2->setLocationSiteEvent($locationSite2);
		$event2->setUser($adminUser);
		$event2->setNbInscriptionMax(15);
		$event2->setState(State::Created);

		// Persist that event to the database
		$manager->persist($event2);

		// Create an event named "Apéro à la plage"
		$event3 = new Event();
		$event3->setName('Apéro à la plage');
		$event3->setStartDateTime(new \DateTime('2021-10-10 10:00:00'));
		$event3->setLimitDateInscription(new \DateTime('2021-10-09 10:00:00'));
		$event3->setEventInfo('Apéro à la plage entre amis');
		$event3->setDuration(new \DateTime('2021-10-10 02:00:00'));
		$event3->setLocationSiteEvent($locationSite3);
		$event3->setUser($basicUser);
		$event3->addUser($adminUser);
		$event3->setNbInscriptionMax(8);
		$event3->setState(State::Closed);

		// Persist that event to the database
		$manager->persist($event3);

		// Create an event named "Randonnée en forêt" (archived)
		$event4 = new Event();
		$event4->setName('Randonnée en forêt');
		$event4->setStartDateTime(new \DateTime('2021-10-10 10:00:00'));
		$event4->setLimitDateInscription(new \DateTime('2021-10-09 10:00:00'));
		$event4->setEventInfo('Randonnée en forêt entre amis');
		$event4->setDuration(new \DateTime('2021-10-10 02:00:00'));
		$event4->setLocationSiteEvent($locationSite3);
		$event4->setUser($basicUser);
		$event4->addUser($adminUser);
		$event4->setNbInscriptionMax(2);
		$event4->setState(State::Closed);

		// Persist that event to the database
		$manager->persist($event4);

		// Create an event named "Soirée jeux de société" (in progress)
		$event5 = new Event();
		$event5->setName('Soirée jeux de société');
		$event5->setStartDateTime(new \DateTime('2021-10-10 10:00:00'));
		$event5->setLimitDateInscription(new \DateTime('2021-10-09 10:00:00'));
		$event5->setEventInfo('Soirée jeux de société entre amis');
		$event5->setDuration(new \DateTime('2021-10-10 02:00:00'));
		$event5->setLocationSiteEvent($locationSite3);
		$event5->setUser($basicUser);
		$event5->addUser($adminUser);
		$event5->setNbInscriptionMax(30);
		$event5->setState(State::InProgress);

		// Persist that event to the database
		$manager->persist($event5);

        $manager->flush();
    }
}
