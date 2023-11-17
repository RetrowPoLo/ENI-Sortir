<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getUser();
        $CurrentUser = $this->getUser();

        if(!$passwordHasher->isPasswordValid($user, 'Pa$$w0rd')) {
        return $this->redirectToRoute('app_event', [
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
        }
        else {
        return $this->redirectToRoute('app_first_login', [
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
        }

    }
}
