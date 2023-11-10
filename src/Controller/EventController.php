<?php

namespace App\Controller;

use App\Entity\State;
use App\Form\EventCancellationType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class EventController extends AbstractController
{
    #[Route('/sortie', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAllNotArchived();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/sortie/details/{id}', name: 'app_event_details')]
    public function details(EventRepository $eventRepository, int $id): Response
    {
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
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
    public function cancel(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, int $id): Response
    {
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
        if($event->getStartDateTime() < new \DateTime()){
            throw new \Exception("impossible d'annuler une sortie qui a débuté");
        }
        $form = $this->createForm(EventCancellationType::class, $event);
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the event's data
            $event = $form->getData();

            $event->setState(State::Canceled);
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('app_event');
        }
        return $this->render('event/cancel.html.twig', [
            'event' => $event,
            'form' => $form
        ]);
    }
    #[Route('/sortie/publish/{id}', name: 'app_event_publish')]
    public function publish(EventRepository $eventRepository, int $id): Response
    {
        return $this->redirectToRoute('app_event');
    }

    #[Route('/sortie/subscribe/{id}', name: 'app_event_subscribe')]
    public function subscribe(EntityManagerInterface $entityManager, EventRepository $eventRepository, UserRepository $userRepository, int $id): Response
    {
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
        $user = $this->getUser();
        $userToAdd = $userRepository->findOneBy(['email'=> $user->getUserIdentifier()]);
        if($event->getState() == State::Open and !$event->getIsTooLateToSubscribe()){
            $event->addUser($userToAdd);
            $entityManager->refresh($event);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_event');
    }

    #[Route('/sortie/unsubscribe/{id}', name: 'app_event_unsubscribe')]
    public function unsubscribe(EntityManagerInterface $entityManager, EventRepository $eventRepository, UserRepository $userRepository, int $id): Response
    {
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
        $user = $this->getUser();
        $userToRemove = $userRepository->findOneBy(['email'=> $user->getUserIdentifier()]);
        if($event->getState() == State::Open and !$event->getIsTooLateToSubscribe()){
            $event->removeUser($userToRemove);
            $entityManager->refresh($event);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_event');
    }

}
