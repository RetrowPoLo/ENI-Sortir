<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/admin/nouvelle-inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
  		// Create a new user
        $user = new User();

		// Create the registration form
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

		// If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
			// Set the user's data
			$user->setEmail($form->get('email')->getData());
			$user->setName($form->get('name')->getData());
			$user->setFirstName($form->get('firstName')->getData());
			$user->setPhone($form->get('phone')->getData());
			$user->setIsActive($form->get('isActive')->getData());
			$user->setUsername($form->get('username')->getData());
			$user->setRoles(['ROLE_USER']);

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

			// Persist the user to the database
            $entityManager->persist($user);
            $entityManager->flush();

			// Redirect to the home page
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}