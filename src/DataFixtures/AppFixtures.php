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
		// ====== Create 5 basic users ======
		// Create a basic user
		$basicUser1 = new User();
		$basicUser1->setEmail('user1@user1.com');
		$basicUser1->setUsername('basic user 1');
		$basicUser1->setFirstName('John');
		$basicUser1->setName('Doe');
		$basicUser1->setPhone('0123456789');
		$basicUser1->setIsActive(true);
		$basicUser1->setPicture('img/imageProfilDefaut.jpg');
		$basicUser1->setRoles(['ROLE_USER']);
		$basicUser1->setForceChange(0);
		$basicUser1Password = $this->passwordHasher->hashPassword(
			$basicUser1,
			'12345'
		);
		$basicUser1->setPassword($basicUser1Password);

		// Persist that basic user to the database
		$manager->persist($basicUser1);

		// Create a basic user
		$basicUser2 = new User();
		$basicUser2->setEmail('user2@user3.com');
		$basicUser2->setUsername('basic user 2');
		$basicUser2->setFirstName('Jack');
		$basicUser2->setName('Doe');
		$basicUser2->setPicture('img/imageProfilDefaut.jpg');
		$basicUser2->setRoles(['ROLE_USER']);
		$basicUser2->setIsActive(false);
		$basicUser2->setForceChange(1);
		$basicUser2Password = $this->passwordHasher->hashPassword(
			$basicUser2,
			'12345'
		);
		$basicUser2->setPassword($basicUser2Password);

		// Persist that basic user to the database
		$manager->persist($basicUser2);

		// Create a basic user
		$basicUser3 = new User();
		$basicUser3->setEmail('user3@user3.com');
		$basicUser3->setUsername('basic user 3');
		$basicUser3->setFirstName('Jane');
		$basicUser3->setName('Smith');
		$basicUser3->setPicture('img/imageProfilDefaut.jpg');
		$basicUser3->setRoles(['ROLE_USER']);
		$basicUser3->setIsActive(true);
		$basicUser3->setForceChange(0);
		$basicUser3Password = $this->passwordHasher->hashPassword(
			$basicUser3,
			'12345'
		);
		$basicUser3->setPassword($basicUser3Password);

		// Persist that basic user to the database
		$manager->persist($basicUser3);

		// Create a basic user
		$basicUser4 = new User();
		$basicUser4->setEmail('user4@user4.com');
		$basicUser4->setUsername('basic user 4');
		$basicUser4->setFirstName('Henry');
		$basicUser4->setName('Doe');
		$basicUser4->setPicture('img/imageProfilDefaut.jpg');
		$basicUser4->setRoles(['ROLE_USER']);
		$basicUser4->setPhone('0123456789');
		$basicUser4->setIsActive(true);
		$basicUser4->setForceChange(0);
		$basicUser4Password = $this->passwordHasher->hashPassword(
			$basicUser4,
			'12345'
		);
		$basicUser4->setPassword($basicUser4Password);

		// Persist that basic user to the database
		$manager->persist($basicUser4);

		// Create a basic user
		$basicUser5 = new User();
		$basicUser5->setEmail('user5@user5.com');
		$basicUser5->setUsername('basic user 5');
		$basicUser5->setFirstName('Helen');
		$basicUser5->setName('Smith');
		$basicUser5->setPicture('img/imageProfilDefaut.jpg');
		$basicUser5->setRoles(['ROLE_USER']);
		$basicUser5->setPhone('0123456789');
		$basicUser5->setIsActive(false);
		$basicUser5->setForceChange(0);
		$basicUser5Password = $this->passwordHasher->hashPassword(
			$basicUser5,
			'12345'
		);
		$basicUser5->setPassword($basicUser5Password);

		// Persist that basic user to the database
		$manager->persist($basicUser5);

		// ====== Create an admin user ======
		// Create an admin user
		$adminUser = new User();
		$adminUser->setEmail('admin@admin.com');
		$adminUser->setUsername('admin user');
		$adminUser->setFirstName('Jane');
		$adminUser->setName('Doe');
		$adminUser->setPhone('0123456789');
		$adminUser->setIsActive(true);
		$adminUser->setRoles(['ROLE_ADMIN']);
		$adminUser->setForceChange(0);
		$adminUser->setPicture('img/imageProfilDefaut.jpg');
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

		// ====== Create 7 events ======
		// Create an event named "Match de foot"
		$event1 = new Event();
		$event1->setName('Match de foot');
		$event1->setStartDateTime(new \DateTime('2023-12-10 10:00:00'));
		$event1->setLimitDateInscription(new \DateTime('2023-11-25 10:00:00'));
		$event1->setEventInfo('Match de foot entre amis');
		$event1->setEndDateTime(new \DateTime('2023-11-12 12:00:00'));
		$event1->setLocationSiteEvent($locationSite1);
		$event1->setNbInscriptionMax(10);
		$event1->setUser($basicUser1);
		$event1->addUser($basicUser2);
		$event1->addUser($basicUser3);
		$event1->addUser($basicUser4);
		$event1->setState(State::Open);
		$event1->setEventLocation($location1);

		// Persist that event to the database
		$manager->persist($event1);

		// Create an event named "Marche à la mairie"
		$event2 = new Event();
		$event2->setName('Marche à la mairie');
		$event2->setStartDateTime(new \DateTime('2023-11-25 10:00:00'));
		$event2->setLimitDateInscription(new \DateTime('2023-11-15 10:00:00'));
		$event2->setEventInfo('Marche à la mairie entre amis');
		$event2->setEndDateTime(new \DateTime('2023-11-12 12:00:00'));
		$event2->setLocationSiteEvent($locationSite2);
		$event2->setUser($basicUser2);
		$event2->setNbInscriptionMax(15);
		$event2->setState(State::Created);
		$event2->setEventLocation($location2);

		// Persist that event to the database
		$manager->persist($event2);

		// Create an event named "Apéro à la plage"
		$event3 = new Event();
		$event3->setName('Apéro à la plage');
		$event3->setStartDateTime(new \DateTime('2023-11-09 10:00:00'));
		$event3->setLimitDateInscription(new \DateTime('2023-11-05 10:00:00'));
		$event3->setEventInfo('Apéro à la plage entre amis');
		$event3->setEndDateTime(new \DateTime('2023-11-12 12:00:00'));
		$event3->setLocationSiteEvent($locationSite3);
		$event3->setUser($basicUser5);
		$event3->addUser($basicUser1);
		$event3->setNbInscriptionMax(8);
		$event3->setState(State::Closed);
		$event3->setEventLocation($location3);

		// Persist that event to the database
		$manager->persist($event3);

		// Create an event named "Randonnée en forêt" (archived)
		$event4 = new Event();
		$event4->setName('Randonnée en forêt');
		$event4->setStartDateTime(new \DateTime('2023-12-01 10:00:00'));
		$event4->setLimitDateInscription(new \DateTime('2023-10-15 10:00:00'));
		$event4->setEventInfo('Randonnée en forêt entre amis');
		$event4->setEndDateTime(new \DateTime('2023-11-12 12:00:00'));
		$event4->setLocationSiteEvent($locationSite3);
		$event4->setUser($basicUser3);
		$event4->addUser($basicUser4);
		$event4->setNbInscriptionMax(2);
		$event4->setState(State::Closed);
		$event4->setEventLocation($location3);

		// Persist that event to the database
		$manager->persist($event4);

		// Create an event named "Soirée jeux de société" (in progress)
		$event5 = new Event();
		$event5->setName('Soirée jeux de société');
		$event5->setStartDateTime(new \DateTime('2023-11-15 10:00:00'));
		$event5->setLimitDateInscription(new \DateTime('2023-11-10 10:00:00'));
		$event5->setEventInfo('Soirée jeux de société entre amis');
		$event5->setEndDateTime(new \DateTime('2023-12-12 12:00:00'));
		$event5->setLocationSiteEvent($locationSite3);
		$event5->setUser($basicUser1);
		$event5->addUser($basicUser2);
		$event5->setNbInscriptionMax(30);
		$event5->setState(State::InProgress);
		$event5->setEventLocation($location3);

		// Persist that event to the database
		$manager->persist($event5);

		// Create an event named "Cinéma" (passed)
		$event6 = new Event();
		$event6->setName('Cinéma');
		$event6->setStartDateTime(new \DateTime('2023-11-12 10:00:00'));
		$event6->setLimitDateInscription(new \DateTime('2023-11-11 10:00:00'));
		$event6->setEventInfo('Cinéma entre amis');
		$event6->setEndDateTime(new \DateTime('2023-11-12 12:00:00'));
		$event6->setLocationSiteEvent($locationSite3);
		$event6->setUser($basicUser1);
		$event6->setNbInscriptionMax(5);
		$event6->setState(State::Passed);
		$event6->setEventLocation($location3);

		// Persist that event to the database
		$manager->persist($event6);

		// Create an event named "Restaurant" (passed)
		$event7 = new Event();
		$event7->setName('Restaurant');
		$event7->setStartDateTime(new \DateTime('2023-11-12 10:00:00'));
		$event7->setLimitDateInscription(new \DateTime('2023-11-11 10:00:00'));
		$event7->setEventInfo('Restaurant entre amis');
		$event7->setEndDateTime(new \DateTime('2023-11-12 12:00:00'));
		$event7->setLocationSiteEvent($locationSite3);
		$event7->setUser($basicUser2);
		$event7->setNbInscriptionMax(5);
		$event7->setState(State::Passed);
		$event7->setEventLocation($location1);

		// Persist that event to the database
		$manager->persist($event7);

        $manager->flush();
    }
}
