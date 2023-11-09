<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\User;
use App\Form\CreateEventCityType;
use App\Form\CreateEventLocationType;
use App\Form\CreateEventType;
use App\Form\CreateEventUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateEventController extends AbstractController
{
    #[Route('/create/event', name: 'app_create_event')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $userid = $this->getUser()->getSitesNoSite();

        $cityRepository = $entityManager->getRepository(City::class);
        $city = $cityRepository->findOneBy(['id' => $userid]);
        $cityName = $city->getName();

        $event = new Event();
        $eventLocation = new Location();
        $eventCity = new City();
        $eventUser = new User();

        $formCreateEvent = $this->createForm(CreateEventType::class, $event);
        $formCreateEventLocation = $this->createForm(CreateEventLocationType::class, $eventLocation);
        $formCreateEventCity = $this->createForm(CreateEventCityType::class, $eventCity);
        $formCreateEventUser = $this->createForm(CreateEventUserType::class, $eventUser);

        return $this->render('create_event/index.html.twig', [
            'formCreateEvent' => $formCreateEvent,
            'formCreateEventLocation' => $formCreateEventLocation,
            'formCreateEventCity' => $formCreateEventCity,
            'formCreateEventUser' => $formCreateEventUser,
            'cityName' => $cityName,
        ]);
    }
}
