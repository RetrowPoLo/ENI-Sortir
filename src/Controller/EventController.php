<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Form\CreateEventType;
use App\Form\EventCancellationType;
use App\Form\EventFilterAdminType;
use App\Form\EventFilterType;
use App\Form\LocationType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\LocationSiteRepository;
use App\Entity\State;
use App\Repository\UserRepository;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/sortie/modifier/{id}', name: 'app_event_edit')]
    public function edit( EntityManagerInterface $entityManager, Request $request, EventService $eventService, EventRepository $eventRepository, int $id): Response
    {
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
        $selectedCity = $event->getEventLocation()->getCity();
        $selectedLocation = $event->getEventLocation();
        $event->getEventLocation()->setCity(new City());
        $event->setEventLocation(new Location);

        $formCreateEvent = $this->createForm(CreateEventType::class, $event, [
            'selected_city' => $selectedCity,
            'selected_location' => $selectedLocation,
        ]);

        $result = $eventService->createEditEvent($entityManager, $request, $event, $this->getUser(), $formCreateEvent);

        $result['params']['title'] = 'Modifier une sortie';
        if($result['view'] == 'event/index.html.twig'){
            return $this->redirectToRoute(('app_event'));
        }
        else{
            return $this->render($result['view'], $result['params']);
        }
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

    #[Route('/sortie/creer', name: 'app_event_create')]
    public function createEvent(Request $request, EntityManagerInterface $entityManager,
                          EventRepository $eventRepository, EventService $eventService): Response
    {
        $event = new Event();
        $formCreateEvent = $this->createForm(CreateEventType::class, $event);
        $result = $eventService->createEditEvent($entityManager, $request, $event, $this->getUser(), $formCreateEvent);
        $result['params']['title'] = 'Créer une sortie';
        if($result['view'] == 'event/index.html.twig'){
            return $this->redirectToRoute(('app_event'));
        }
        else{
            return $this->render($result['view'], $result['params']);
        }
    }

    #[Route('/get-zipcode/{city}-{location}', name: 'get_zipcode', methods: ['GET'])]
    public function getZipcodeAction(City $city, Location $location = null): JsonResponse
    {
        // Assuming $city is the selected City entity from the database
        $zipcode = $city->getZipcode();

        if ($location === null) {
            $locationList = '';
            $street = '';
        } else {
            $locationList = $location->getCity();
            $street = $location->getStreet();
        }

        return new JsonResponse([
            'zipcode' => $zipcode,
            'street' => $street,
            'locationList' => $locationList,
        ]);
    }
    #[Route('/get-locations/{city}', name: 'get_locations', methods: ['GET'])]
    public function getLocationsAction(City $city): JsonResponse
    {
        $locations = $city->getLocations();

        $locationData = [];
        foreach ($locations as $location) {
            $locationData[] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
            ];
        }

        return new JsonResponse(['locations' => $locationData]);
    }

    #[Route('/sortie/lieu/nouveau', name: 'app_event_location_create')]
    public function createLocation(EntityManagerInterface $entityManager, CityRepository $cityRepository, Request $request): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData();
            $entityManager->persist($location);
            $entityManager->flush();
            return $this->redirectToRoute('app_event');
        }
        return $this->render('event/newLocation.html.twig', [
            'form' => $form
        ]);
    }
}
