<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if ($user->getId() == $request->get('id') || $this->isGranted('ROLE_ADMIN')) {
        return $this->render('profile/profile.html.twig');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas afficher le profil d\'un autre utilisateur.');
            return $this->redirectToRoute('app_home');
        }

    }

//    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('user/edit.html.twig', [
//            'user' => $user,
//            'form' => $form,
//        ]);
//    }

    #[Route('/editProfile/{userid}', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

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
            }

            return $this->render('profile/edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
//            'form' => $form,
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
