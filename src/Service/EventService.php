<?php
namespace App\Service;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\LocationSite;
use App\Entity\State;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use App\Repository\LocationSiteRepository;
use function PHPUnit\Framework\equalTo;

class EventService
{
    private  $cityRepository = null;
    private  $locationRepository = null;
    private $locationSiteRepos = null;
    public function __construct(CityRepository $cityRepository, LocationRepository $locationRepository, LocationSiteRepository $locationSiteRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->locationRepository = $locationRepository;
        $this->locationSiteRepos = $locationSiteRepository;
    }
    public function createEditEvent($entityManager, $request, Event $event, $user, $formCreateEvent): array{
        //$userLocationSiteId = $user->getSitesNoSite();
        $error = "";

        $formCreateEvent->handleRequest($request);

        if ($formCreateEvent->isSubmitted() && $formCreateEvent->isValid()) {
            $Getlocation = $formCreateEvent->get("eventLocation")->getData()->getName();
            if ($Getlocation === null) {
                $error = "vous avez besoin de créer un lieu si non existant";
                return ['view' => "create_event/index.html.twig", 'params' => [
                    'formCreateEvent' => $formCreateEvent,
                    'errorLocation' => $error,
                    'errorStartTime' => '',
                    'errorEndTime' => '',
                    'errorLimitTime' => '',
                ]];
            }
            $locationSite = $this->locationSiteRepos->find(['id'=> '1']);
            $event->setLocationSiteEvent($locationSite);
            $event->setUser($user);
            if ('publish' === $formCreateEvent->getClickedButton()->getName()) {
                $event->setState(State::Open);
            }
            $requestData =$request->request->all();
            $eventLocation = $requestData['create_event']['eventLocation'];

            $locationData = $this->locationRepository->find($eventLocation['name']);
            $event->setEventLocation($locationData);
            $selectedLocationSite = $this->locationSiteRepos->findOneBy(['id' => $event->getLocationSiteEvent()->getId()]);
            $event->setLocationSiteEvent($selectedLocationSite);

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
            }
            else {
                //dd($event);
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
