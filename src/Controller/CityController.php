<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CitySearchType;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_')]
class CityController extends AbstractController
{
    #[Route('/villes', name: 'city')]
    public function index(CityRepository $cityRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
		// Create a new city
        $city = new City();

		// Create the city form
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

		// If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
			// Set the city's data
            $city = $form->getData();

			// Persist the city to the database
            $entityManager->persist($city);
            $entityManager->flush();

			// Redirect to the city page
            return $this->redirectToRoute('app_city');
        }

		// Create the city search form
        $formSearch = $this->createForm(CitySearchType::class, $city);
        $formSearch->handleRequest($request);

		// If the form is submitted and valid
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $city = $formSearch->getData();
            $query = $city->getName();
            $cities = $cityRepository->findBy(
                ['name' => $query],
            );
            if ($query === null){
                $cities = $cityRepository->findAll();
            }
        }
        else{
            $cities = $cityRepository->findAll();
        }

        return $this->render('city/cities.html.twig', [
            'cities' => $cities,
            'form' => $form,
            'formSearch' => $formSearch,
            'user' => $user,
            'currentUserId' => $CurrentUser,
        ]);
    }

    #[Route('/villes/modifier/{id}', name: 'city_edit', requirements: ['id' => '\d+'])]
    public function edit(CityRepository $cityRepository, Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $this->getUser();
        $CurrentUser = $this->getUser();
        $city = $cityRepository->find($id);
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cityEdited = $form->getData();
            $entityManager->persist($cityEdited);
            $entityManager->flush();
            return $this->redirectToRoute('app_city');
        }
        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
            'user' => $user,
            'currentUserId' => $CurrentUser
        ]);
    }

    #[Route('/ville/supprimer/{id}', name: 'city_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, City $city): Response
    {
		// Delete the city and redirect to the city page
        $entityManager->remove($city);
        $entityManager->flush();

        return $this->redirectToRoute('app_city');
    }
}
