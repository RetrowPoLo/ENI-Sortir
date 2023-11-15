<?php
namespace App\Service;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\State;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use function PHPUnit\Framework\equalTo;

class EventService
{
    private  $cityRepository = null;
    private  $locationRepository = null;
    public function __construct(CityRepository $cityRepository, LocationRepository $locationRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->locationRepository = $locationRepository;
    }
    public function createEditEvent($entityManager, $request, Event $event, $user, $formCreateEvent): array{
        $userLocationSiteId = $user->getSitesNoSite();
        $error = "";

        $formCreateEvent->handleRequest($request);

        if ($formCreateEvent->isSubmitted() && $formCreateEvent->isValid()) {
            $event->setLocationSiteEvent($userLocationSiteId);
            $event->setUser($user);
            if ('publish' === $formCreateEvent->getClickedButton()->getName()) {
                $event->setState(State::Open);
            }
            $requestData =$request->request->all();
            $eventLocation = $requestData['create_event']['eventLocation'];

            $locationData = $this->locationRepository->find($eventLocation['name']);
            $event->setEventLocation($locationData);

            $time = new \DateTime('now', new \DateTimeZone('UTC'));
            $startDateTime = $formCreateEvent->get("startDateTime")->getData();
            $endDateTime = $formCreateEvent->get("endDateTime")->getData();
            $limitDateInscription = $formCreateEvent->get("limitDateInscription")->getData();

            if ($startDateTime < $time) {
                $error = "La date de début de la sortie doit être supérieur a la date actuelle";
                return ['view' => "create_event/index.html.twig", 'params' => [
                    'formCreateEvent' => $formCreateEvent,
                    'errorStartTime' => $error,
                    'errorEndTime' => '',
                    'errorLimitTime' => '',
                    'errorLocation' => '',
                ]];
            } elseif ($endDateTime < $startDateTime) {
                $error = "La date de fin de la sortie doit être supérieur a La date de début de la sortie";
                return ['view' => "create_event/index.html.twig", 'params' => [
                    'formCreateEvent' => $formCreateEvent,
                    'errorStartTime' => '',
                    'errorEndTime' => $error,
                    'errorLimitTime' => '',
                    'errorLocation' => '',
                ]];
            } elseif ($limitDateInscription < $time || $limitDateInscription > $startDateTime) {
                $error = "La date de fin d'inscription doit être inférieur à La date de début de la sortie et/ou être supérieur à la date du jour";
                return ['view' => "create_event/index.html.twig", 'params' => [
                    'formCreateEvent' => $formCreateEvent,
                    'errorStartTime' => '',
                    'errorEndTime' => '',
                    'errorLimitTime' => $error,
                    'errorLocation' => '',
                ]];
            } elseif ($event->getEventLocation() == null) {
                $error = "Le lieu ne peux pas être vide";
                return ['view' => "create_event/index.html.twig", 'params' => [
                    'formCreateEvent' => $formCreateEvent,
                    'errorStartTime' => '',
                    'errorEndTime' => '',
                    'errorLimitTime' => '',
                    'errorLocation' => $error,
                ]];
            }
            else {
                $entityManager->persist($event);
                $entityManager->flush();
                return ['view' => "event/index.html.twig", 'params' => [
                    'events' => null
                ]];
            }
        }

        return ['view' => "create_event/index.html.twig", 'params' => [
            'formCreateEvent' => $formCreateEvent,
            'errorStartTime' => $error,
            'errorEndTime' => $error,
            'errorLimitTime' => $error,
            'errorLocation' => $error,
        ]];
    }
}