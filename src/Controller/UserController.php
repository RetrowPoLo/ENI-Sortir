<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_index', methods: ['GET'])]
    public function index2(UserRepository $userRepository): Response
    {
        $CurrentUser = $this->getUser();

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'currentUserId' => $CurrentUser,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $CurrentUser = $this->getUser();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration/register.html.twig', [
            'user' => $user,
            'registrationForm' => $form,
            'currentUserId' => $CurrentUser
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $CurrentUser = $this->getUser();

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
//        $user = $this->getUser();

        $CurrentUser = $this->getUser();
//
        $cityName = null;
        $userLocationSiteId = $this->getUser()->getSitesNoSite();

        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userLocationSiteId]);

        if($city) {
            $cityName = $city->getName();
        }

//        $initPassword = $user->getPassword();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            $entityManager->flush();
//            $userModif = $form->getData();
//
//				$hashedPassword = $passwordHasher->hashPassword(
//					$userModif,
//					$userModif->getPassword());
//
//                $user->setPassword($hashedPassword);


            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

			return $this->redirectToRoute('app_profile', [
                'id' => $request->get('id'),
                'cityName' => $cityName,
                'currentUserId' => $CurrentUser,
            ]);
        }
//        else{
//            $user->setPassword($initPassword);
//        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'currentUserId' => $CurrentUser,
            'form' => $form,
            'cityName' => $cityName,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
