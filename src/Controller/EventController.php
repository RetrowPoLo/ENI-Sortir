<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\State;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/sortie', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/sortie/details/{id}', name: 'app_event_details')]
    public function details(EventRepository $eventRepository, int $id): Response
    {
        $event = $eventRepository->findOneBy(['id'=> $id]);
        return $this->render('event/details.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/sortie/edit/{id}', name: 'app_event_edit')]
    public function edit(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event');
    }

    #[Route('/sortie/cancel/{id}', name: 'app_event_cancel')]
    public function cancel(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event');
    }
    #[Route('/sortie/publish/{id}', name: 'app_event_publish')]
    public function publish(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event');
    }

    #[Route('/sortie/subscribe/{id}', name: 'app_event_subscribe')]
    public function subscribe(EntityManagerInterface $entityManager, EventRepository $eventRepository, int $id, User $user): Response
    {
        $event = $eventRepository->findOneBy(['id'=> $id]);
        var_dump($event->getUsers()[0]->getId());
        if($event->getState() == State::Open and !$event->getIsTooLateToSubscribe()){
            $event->addUser($user);
            $entityManager->persist($event);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_event');
    }

}
