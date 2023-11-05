<?php

namespace App\DataFixtures;

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
   		// Create a default user
		$defaultUser = new User();
		$defaultUser->setEmail('user@user.com');
		$defaultUser->setUsername('default user');
		$defaultUser->setRoles(['ROLE_USER']);
		$defaultUserPassword = $this->passwordHasher->hashPassword(
			$defaultUser,
			'12345'
		);
		$defaultUser->setPassword($defaultUserPassword);

		// Persist that default user to the database
		$manager->persist($defaultUser);

		// Create an admin user
		$adminUser = new User();
		$adminUser->setEmail('admin@admin.com');
		$adminUser->setUsername('admin user');
		$adminUser->setRoles(['ROLE_ADMIN']);
		$adminUserPassword = $this->passwordHasher->hashPassword(
			$adminUser,
			'12345'
		);
		$adminUser->setPassword($adminUserPassword);

		// Persist that admin user to the database
		$manager->persist($adminUser);

        $manager->flush();
    }
}
