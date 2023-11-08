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

		// Create 10 basic users
		for ($i = 1; $i <= 10; $i++) {
			$this->createUserBasicFixtures($manager, $i);
		}

        $manager->flush();
    }

	/**
	 * Create a basic user
	 * @param ObjectManager $manager - The object manager
	 * @param int $i - The number of the user
	 */
	private function createUserBasicFixtures(ObjectManager $manager, int $i): void
	{
		// Create a basic user
		$basicUser = new User();
		$basicUser->setEmail('user' . $i . '@user.com');
		$basicUser->setUsername('basic user ' . $i);
		$basicUser->setFirstName('John' . $i);
		$basicUser->setName('Doe' . $i);
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
	}
}
