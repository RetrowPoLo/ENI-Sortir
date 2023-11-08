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
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        var_dump($events[0]->getIsTooLateToSubscribe());
        var_dump($events[1]->getIsTooLateToSubscribe());
        return $this->render('event/index.html.twig', [
            'events' => $events,
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

    #[Route('/sortie/details/{id}', name: 'app_event_details')]
    public function details(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event_new');
    }

    #[Route('/sortie/edit/{id}', name: 'app_event_edit')]
    public function edit(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event_new');
    }

    #[Route('/sortie/cancel/{id}', name: 'app_event_cancel')]
    public function cancel(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event_new');
    }
    #[Route('/sortie/cancel/{id}', name: 'app_event_publish')]
    public function publish(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event_new');
    }

}
