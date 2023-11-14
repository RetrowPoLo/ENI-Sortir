<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\State;
use App\Entity\User;
use App\Form\CreateEventCityType;
use App\Form\CreateEventLocationType;
use App\Form\CreateEventType;
use App\Form\CreateEventUserType;
use App\Repository\EventRepository;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateEventController extends AbstractController
{
    #[Route('/create/event', name: 'app_create_event')]
    public function index(Request $request, EntityManagerInterface $entityManager,
            EventRepository $eventRepository, EventService $eventService): Response
    {
        $event = new Event();

        $formCreateEvent = $this->createForm(CreateEventType::class, $event);

        $result = $eventService->createEditEvent($entityManager, $request,
            $event, $this->getUser(), $formCreateEvent);
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
}
