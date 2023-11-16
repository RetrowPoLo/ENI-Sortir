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
        $CurrentUser = $this->getUser();

		// Create the registration form
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

		// If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
			// Set the user's data
			$user = $form->getData();
			$user->setRoles(['ROLE_USER']);

            $pseudo = $this->genererChaineAleatoire();
            $user->setUsername($pseudo);

            $password = 'Pa$$w0rd';

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            $user->setPicture(null);

//            $user->setForceChange(1);

			// Persist the user to the database
            $entityManager->persist($user);
            $entityManager->flush();

			// Redirect to the home page
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'currentUserId' => $CurrentUser,
        ]);
    }

    function genererChaineAleatoire($longueur = 10)
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < $longueur; $i++)
        {
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
        }
        return $chaineAleatoire;
    }
}
