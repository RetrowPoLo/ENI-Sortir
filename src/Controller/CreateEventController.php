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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateEventController extends AbstractController
{
    #[Route('/create/event', name: 'app_create_event')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        /** @var User $user */
        $cityName = null;
        $userLocationSiteId = $this->getUser()->getSitesNoSite();
        $userId = $this->getUser();
        $CurrentUser = $this->getUser();

        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userLocationSiteId]);
        if($city){
            $cityName = $city->getName();
        }

        $error = "";

        $event = new Event();
        $eventCity = new City();
        $eventUser = new User();

        $eventLocation = new Location();

        $formCreateEvent = $this->createForm(CreateEventType::class, $event);
        $formCreateEventLocation = $this->createForm(CreateEventLocationType::class, $eventLocation);
        $formCreateEventCity = $this->createForm(CreateEventCityType::class, $eventCity);
        $formCreateEventUser = $this->createForm(CreateEventUserType::class, $eventUser);

        $formCreateEvent->handleRequest($request);
        $formCreateEventLocation->handleRequest($request);
        if ($formCreateEvent->isSubmitted() && $formCreateEvent->isValid()) {
            $event->setLocationSiteEvent($userLocationSiteId);
            $event->setUser($userId);
            if ('publish' === $formCreateEvent->getClickedButton()->getName()) {
                $event->setState(State::Open);
            }
            $event->setEventLocation($formCreateEventLocation->get("name")->getData());

            // Obtenez la date actuelle au format UTC
            $time = new \DateTime('now', new \DateTimeZone('UTC'));
            $startDateTime = $formCreateEvent->get("startDateTime")->getData();

            $endDateTime = $formCreateEvent->get("endDateTime")->getData();
            $limitDateInscription = $formCreateEvent->get("limitDateInscription")->getData();
            $locationName = $formCreateEventLocation->get("name")->getData();


                if ($startDateTime < $time) {
                $error = "La date de début de la sortie doit être supérieur a la date actuelle";
                return $this->render('create_event/index.html.twig', [
                    'formCreateEvent' => $formCreateEvent,
                    'formCreateEventLocation' => $formCreateEventLocation,
                    'formCreateEventCity' => $formCreateEventCity,
                    'formCreateEventUser' => $formCreateEventUser,
                    'cityName' => $cityName,
                    'errorStartTime' => $error,
                    'errorEndTime' => '',
                    'errorLimitTime' => '',
                    'errorLocation' => '',
                    'currentUserId'  => $CurrentUser,
                ]);
            } elseif ($endDateTime < $startDateTime) {
                    $error = "La date de fin de la sortie doit être supérieur a La date de début de la sortie";
                    return $this->render('create_event/index.html.twig', [
                        'formCreateEvent' => $formCreateEvent,
                        'formCreateEventLocation' => $formCreateEventLocation,
                        'formCreateEventCity' => $formCreateEventCity,
                        'formCreateEventUser' => $formCreateEventUser,
                        'cityName' => $cityName,
                        'errorStartTime' => '',
                        'errorEndTime' => $error,
                        'errorLimitTime' => '',
                        'errorLocation' => '',
                        'currentUserId'  => $CurrentUser,
                    ]);
            } elseif ($limitDateInscription < $time || $limitDateInscription > $startDateTime) {
                    $error = "La date de fin d'inscription doit être inférieur à La date de début de la sortie et/ou être supérieur à la date du jour";
                    return $this->render('create_event/index.html.twig', [
                        'formCreateEvent' => $formCreateEvent,
                        'formCreateEventLocation' => $formCreateEventLocation,
                        'formCreateEventCity' => $formCreateEventCity,
                        'formCreateEventUser' => $formCreateEventUser,
                        'cityName' => $cityName,
                        'errorStartTime' => '',
                        'errorEndTime' => '',
                        'errorLimitTime' => $error,
                        'errorLocation' => '',
                        'currentUserId'  => $CurrentUser,
                    ]);
            } elseif ($locationName === null) {
            $error = "Le lieu ne peux pas être vide";
            return $this->render('create_event/index.html.twig', [
                'formCreateEvent' => $formCreateEvent,
                'formCreateEventLocation' => $formCreateEventLocation,
                'formCreateEventCity' => $formCreateEventCity,
                'formCreateEventUser' => $formCreateEventUser,
                'cityName' => $cityName,
                'errorStartTime' => '',
                'errorEndTime' => '',
                'errorLimitTime' => '',
                'errorLocation' => $error,
                'currentUserId'  => $CurrentUser,
            ]);
        }
            else {
                $entityManager->persist($event);
                $entityManager->flush();
            }
        }

        return $this->render('create_event/index.html.twig', [
            'formCreateEvent' => $formCreateEvent,
            'formCreateEventLocation' => $formCreateEventLocation,
            'formCreateEventCity' => $formCreateEventCity,
            'formCreateEventUser' => $formCreateEventUser,
            'cityName' => $cityName,
            'errorStartTime' => $error,
            'errorEndTime' => $error,
            'errorLimitTime' => $error,
            'errorLocation' => $error,
            'currentUserId'  => $CurrentUser,
        ]);
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
