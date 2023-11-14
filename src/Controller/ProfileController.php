<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\FirstLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(User $user): Response
    {
        return $this->render('profile/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/premiere_connexion', name: 'app_first_login', methods: ['GET', 'POST'])]
    public function firstLog( Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $initPseudo = $user->getUsername();

            $form = $this->createForm(FirstLoginType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $userModif = $form->getData();

                if ($initPseudo !== $userModif->getUsername()) {

                    $hashedPassword = $passwordHasher->hashPassword(
                        $userModif,
                        $userModif->getPassword()
                    );
                    $user->setPassword($hashedPassword);
                    $user->setUsername($userModif->getUsername());
                    $user->setForceChange(0);

                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Profil mis à jour avec succès.');
                    return $this->redirectToRoute('app_home');
                } else {
                    echo '<div class="alert alert-danger">votre pseudo n\'a pas été modifié.</div>';
                    $entityManager->refresh($user);
                }

            } else {
                $entityManager->refresh($user);
            }

            return $this->render('profile/first_login.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
//            'form' => $form,
            ]);
    }

    #[Route('/editProfile/{userid}', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
        $userLocationSiteId = $this->getUser()->getSitesNoSite();
        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userLocationSiteId]);
        $cityName = $city->getName();

        if ($user->getId() == $request->get('userid') || $this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(EditUserType::class, $user);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $userModif = $form->getData();

                $hashedPassword = $passwordHasher->hashPassword(
                    $userModif,
                    $userModif->getPassword()
                );
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès.');
                return $this->redirectToRoute('app_profile', ['id' => $request->get('userid')]);
            } else {
                $entityManager->refresh($user);
            }

            return $this->render('profile/edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
                'currentUserId' => $CurrentUser,
                'cityName' => $cityName,
//              'form' => $form,
            ]);
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas modifier le profil d\'un autre utilisateur.');
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }

    }
//    #[Route('/uploadImage', name: 'user_upload')]
//    public function image(Request $request): Response
//    {
//        $user = $this->getUser();
//        return 'ok';
//
//    }
}
