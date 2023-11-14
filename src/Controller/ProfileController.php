<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\EditUserType;
use App\Form\FirstLoginType;
use App\Repository\UserRepository;
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

    #[Route('/editProfile/{userid}/motdepasse', name: 'edit_password', methods: ['GET', 'POST'])]
    public function editPasswd( UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('password')->getData();

            $userModif = $form->getData();

            if($passwordHasher->isPasswordValid($user, $oldPassword)) {

                $hashedPassword = $passwordHasher->hashPassword(
                    $userModif,
                    $newPassword
                );
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès.');
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('error', 'L\'Ancien mot de passe est incorrect.');
//                echo '<div class="alert alert-danger">L\'Ancien mot de passe est incorrect.</div>';
                $entityManager->refresh($user);
            }
        } else {
            $entityManager->refresh($user);
        }

        return $this->render('profile/edit_passwd.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
//            'form' => $form,
        ]);
    }

    #[Route('/user/premiere_connexion', name: 'app_first_login', methods: ['GET', 'POST'])]
    public function firstLog(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $oldUsername  = $userRepository->findOneBySomeId($user->getUserIdentifier());
        $oldUsername = $oldUsername->getUsername();

        $form = $this->createForm(FirstLoginType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userModif = $form->getData();

            if ($oldUsername != $userModif->getUsername()) {

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
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
        $cityName = null;
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
        $userLocationSiteId = $this->getUser()->getSitesNoSite();
        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userLocationSiteId]);

        if ($city) {
            $cityName = $city->getName();
        }

        if ($user->getId() == $request->get('userid') || $this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(EditUserType::class, $user);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès.');
                return $this->redirectToRoute('app_home');
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
            return $this->redirectToRoute('app_profile_edit', ['userid' => $user->getId()]);
        }

    }

    //    #[Route('/editProfile/{userid}/photo', name: 'user_upload')]
//    public function upload(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $user = $this->getUser();
//
//        $previousProfilePicture = $user->getPicture();
//
//        $form = $this->createForm(ProfileUploadType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted()) {
//            if ($form->isValid()) {
//                $safeFilename = bin2hex(random_bytes(10)) . uniqid();
//                $newFilename = $safeFilename.'.'.$user->getPicture()->guessExtension();
//                $user->getPicture()->move($this->getParameter('profile_pic_dir'), $newFilename);
//
//                $user->setPicture($newFilename);
//
//                $user->setPicture(null);
//
//                $entityManager->persist($user);
//                $entityManager->flush();
//
//                if (!empty($previousProfilePicture)){
//                    $filelocation = $this->getParameter('profile_pic_dir') . "/" . $previousProfilePicture;
//                    if (file_exists($filelocation)){
//                        unlink($filelocation);
//                    }
//                    $this->addFlash('success', 'Photo de profil modifiée !');
//                }
//                else {
//                    $this->addFlash('success', 'Photo de profil ajoutée !');
//                }
//
//                return $this->redirectToRoute('user_profile', ["id" => $user->getId()]);
//            }
//
//            $user->setPicture(null);
//        }
//
//        $entityManager->refresh($user);
//
//        return $this->render('user/upload.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
//    #[Route('/uploadImage', name: 'user_upload')]
//    public function image(Request $request): Response
//    {
//        $user = $this->getUser();
//        return 'ok';
//
//    }
}
