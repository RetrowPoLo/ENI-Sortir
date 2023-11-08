<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/sortie', name: 'app_event')]
    public function index(): Response
    {

        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/sortie/creer', name: 'app_event_new')]
    public function create(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $city = $form->getData();
            $entityManager->persist($city);
            $entityManager->flush();
            return $this->redirectToRoute('app_event');
        }
        return $this->render('event/new.html.twig', [
            'form' => $form,
        ]);
    }
}
