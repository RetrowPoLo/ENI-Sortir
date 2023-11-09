<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\LocationSite;
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

        $manager->flush();
    }
}
