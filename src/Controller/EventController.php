<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\LocationSite;
use App\Entity\User;
use App\Form\CreateEventCityType;
use App\Form\CreateEventLocationType;
use App\Form\CreateEventType;
use App\Form\CreateEventUserType;
use App\Form\EventCancellationType;
use App\Form\EventFilterAdminType;
use App\Form\EventFilterType;
use App\Form\LocationType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use App\Repository\LocationSiteRepository;
use App\Entity\State;
use App\Repository\UserRepository;
use App\Service\EventService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\Container;
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
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
		// Find all events
		$events = $this->eventRepository->findAllNotArchived();
        $firstLocationsSite = $this->locationSiteRepository->findAll()[0];
		// If the current user is an admin render the admin filter form instead of the user filter form
		$this->isGranted('ROLE_ADMIN') ? $formFilter =
            $this->createForm(EventFilterAdminType::class, null, options: ['selectedLocationSite'=>$firstLocationsSite]) :
            $formFilter = $this->createForm(EventFilterType::class,  null, options: ['selectedLocationSite'=>$firstLocationsSite]);
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
                'user' => $user,
                'currentUserId' => $CurrentUser,
			]);
		}

        return $this->render('event/index.html.twig', [
            'events' => $events,
			'formFilter' => $formFilter->createView(),
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
    }

	/**
	 * @throws \Exception
	 */
	#[Route('/sortie/details/{id}', name: 'app_event_details')]
    public function details(EventRepository $eventRepository, int $id): Response
    {
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
        $event = $eventRepository->findOneByIdNotArchived($id);
        if($event == null){
            throw new \Exception("impossible de trouver la sortie avec l'id: ".$id);
        }
        return $this->render('event/details.html.twig', [
            'event' => $event,
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
    }

    #[Route('/sortie/modifier/{id}', name: 'app_event_edit')]
    public function edit(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        CityRepository $cityRepository,
        LocationRepository $locationRepository,
        Request $request,
        EventService $eventService,
        int $id
    ): Response
    {
        $cityName = null;
        $userLocationSiteId = $this->getUser()->getSitesNoSite();
        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userLocationSiteId]);
        if($city){
            $cityName = $city->getName();
        }
        $error = "";
        $event = new Event();
        try {
            $event = $eventRepository->findOneByIdNotArchived($id);
            if($event == null){
                $error = "impossible de trouver la sortie avec l'id: ".$id;
            }
        } catch (NonUniqueResultException $e) {
            $error = $e->getMessage();
        }
        $selectedCity = $cityRepository->findOneBy(['id'=> $event->getEventLocation()->getCity()->getId()] );
        $selectedLocation = $locationRepository->findOneBy(['id' => $event->getEventLocation()->getId()]);

        $event->setEventLocation(new Location());
        $event->getEventLocation()->setCity(new City());

        $formCreateEvent = $this->createForm(CreateEventType::class, $event,
            options: ['selectedCity'=>$selectedCity, 'selectedLocation' => $selectedLocation]);

        $result = $eventService->createEditEvent($entityManager, $request, $event, $this->getUser(), $formCreateEvent);

        $result['params']['title'] = 'Modifier une sortie';
        $result['params']['cityName'] = $cityName;
        $result['params']['editing'] = true;
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
	#[Route('/sortie/annuler/{id}', name: 'app_event_cancel')]
    public function cancel(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, int $id): Response
    {
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
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
            'form' => $form,
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
    }
    #[Route('/sortie/publier/{id}', name: 'app_event_publish')]
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
	#[Route('/sortie/s\'inscrire/{id}', name: 'app_event_subscribe')]
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
	#[Route('/sortie/se-désinscrire/{id}', name: 'app_event_unsubscribe')]
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
        $cityName = null;
        $userLocationSiteId = $this->getUser()->getSitesNoSite();
        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userLocationSiteId]);
        if($city){
            $cityName = $city->getName();
        }
        $event = new Event();
        $formCreateEvent = $this->createForm(CreateEventType::class, $event);
        $result = $eventService->createEditEvent($entityManager, $request, $event, $this->getUser(), $formCreateEvent);
        $result['params']['title'] = 'Créer une sortie';
        $result['params']['cityName'] = $cityName;
        $result['params']['editing'] = false;
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
