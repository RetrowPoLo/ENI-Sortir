<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateEventController extends AbstractController
{
    #[Route('/create/event', name: 'app_create_event')]
    public function index(): Response
    {
        return $this->render('create_event/index.html.twig', [
            'controller_name' => 'CreateEventController',
        ]);
    }
}
