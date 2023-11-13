<?php

namespace App\Controller;

use App\Form\EventCancellationType;
use App\Form\EventFilterAdminType;
use App\Form\EventFilterType;
use App\Repository\EventRepository;
use App\Repository\LocationSiteRepository;
use App\Entity\State;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
	public function __construct(
		private LocationSiteRepository $locationSiteRepository,
		private EventRepository $eventRepository,
	)
	{
	}

	#[Route('/sortie', name: 'app_event')]
    public function index(Request $request): Response
	{
		// Find all events
		$events = $this->eventRepository->findAllNotArchived();

		// If the current user is an admin render the admin filter form instead of the user filter form
		$this->isGranted('ROLE_ADMIN') ? $formFilter = $this->createForm(EventFilterAdminType::class) : $formFilter = $this->createForm(EventFilterType::class);
		$formFilter->handleRequest($request);

		// Check if the form is submitted and valid
		if ($formFilter->isSubmitted() && $formFilter->isValid()) {
			// Get the location site id from the form
			$locationSiteId = $formFilter->get('locationSiteEvent')->getData();
			$locationSiteId ? $locationSiteId = $locationSiteId->getId() : $locationSiteId = 0;

			// Get the start date time from the form
			$startDateTime = $formFilter->get('startDateTime')->getData();
			$startDateTime ? $startDateTime = new \DateTime($startDateTime->format('Y-m-d H:i:s')) : $startDateTime = null;

			// Get the end date time from the form
			$endDateTime = $formFilter->get('endDateTime')->getData();
			$endDateTime ? $endDateTime = new \DateTime($endDateTime->format('Y-m-d H:i:s')) : $endDateTime = null;

			// If the current user is an admin, get the state from the form
			if ($this->isGranted('ROLE_ADMIN')) {
				$state = $formFilter->get('state')->getData();
			}

			// Get the filtered result
			if ($this->isGranted('ROLE_ADMIN')) {
				$filteredResult = $this->eventRepository->findByAdminFilters(
					$locationSiteId,
					$formFilter->get('name')->getData(),
					$startDateTime,
					$endDateTime,
					$state,
				);
			} else {
				$filteredResult = $this->eventRepository->findByFilters(
					$locationSiteId,
					$formFilter->get('name')->getData(),
					$startDateTime,
					$endDateTime,
					$this->getUser(),
					$formFilter->get('userIsOrganizer')->getData(),
					$formFilter->get('userIsRegistered')->getData(),
					$formFilter->get('userIsNotRegistered')->getData(),
					$formFilter->get('stateIsPassed')->getData(),
				);
			}

			return $this->render('event/index.html.twig', [
				'events' => $filteredResult,
				'formFilter' => $formFilter->createView(),
			]);
		}

        return $this->render('event/index.html.twig', [
            'events' => $events,
			'formFilter' => $formFilter->createView(),
        ]);
    }

	/**
	 * @throws \Exception
	 */
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

	/**
	 * @throws NonUniqueResultException
	 * @throws \Exception
	 */
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
    public function publish(EventRepository $eventRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
        $event->setState(State::Open);
        $entityManager->persist($event);
        $entityManager->flush();
        return $this->redirectToRoute('app_event');
    }

	/**
	 * @throws NonUniqueResultException
	 * @throws \Exception
	 */
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

	/**
	 * @throws NonUniqueResultException
	 * @throws \Exception
	 */
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
