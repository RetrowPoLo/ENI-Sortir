<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFilterType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\LocationSiteRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Request $request, EventRepository $eventRepository): Response
    {
		// Find all location sites
		$locationSites = $this->locationSiteRepository->findAll();

		// Find all events
		$events = $this->eventRepository->findAll();

		$formFilter = $this->createForm(EventFilterType::class);
		$formFilter->handleRequest($request);

		if ($formFilter->isSubmitted() && $formFilter->isValid()) {
			$filteredResult = $this->eventRepository->findByFilters(
				'',
				$formFilter->get('name')->getData(),
				null,
				null,
				false,
				false,
				false,
				false
			);

			return $this->render('event/index.html.twig', [
				'locationSites' => $locationSites,
				'events' => $filteredResult,
				'formFilter' => $formFilter->createView(),
			]);
		}

        return $this->render('event/index.html.twig', [
			'locationSites' => $locationSites,
            'events' => $events,
			'formFilter' => $formFilter->createView(),
        ]);
    }

    #[Route('/sortie/creer', name: 'app_event_new')]
    public function create(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $city = $form->getData();
            $entityManager->persist($city);
            $entityManager->flush();
            return $this->redirectToRoute('app_event');
        }
        return $this->render('event/new.html.twig', [
            'form' => $form,
        ]);
    }
}
